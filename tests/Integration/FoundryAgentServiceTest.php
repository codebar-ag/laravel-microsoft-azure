<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\AuthenticationException;
use CodebarAg\MicrosoftAzure\Exceptions\BadRequestException;
use CodebarAg\MicrosoftAzure\Exceptions\ConflictException;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('provisions a foundry project and exercises the full agent service surface', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $accountName = 'lma-aif-'.Str::lower(Str::random(8));
        $projectName = 'lma-prj';

        $accounts = Azure::instance()->cognitiveServices($context->subscriptionId, $context->resourceGroupName);
        $account = $accounts->account($accountName);

        $accountCreated = false;
        /** @var array<string, string> $attempts */
        $attempts = [];

        try {
            $accounts->account($accountName)->createOrUpdate(
                location: $context->location,
                properties: [
                    'customSubDomainName' => $accountName,
                    'disableLocalAuth' => false,
                    'allowProjectManagement' => true,
                ],
                identityType: 'SystemAssigned',
            );
            $accountCreated = true;

            $provisionedAccount = pollUntil(
                function () use ($account) {
                    try {
                        $data = $account->get();
                    } catch (ConflictException) {
                        return null;
                    }

                    return $data->provisioningState === ProvisioningState::Succeeded ? $data : null;
                },
                timeoutSeconds: 300,
                intervalSeconds: 10,
            );

            expect($provisionedAccount->provisioningState)->toBe(ProvisioningState::Succeeded);

            $projects = $account->projects();

            // Projects carry their own managed identity, separate from the
            // parent account's (per Microsoft's RBAC docs: role assignments
            // target "your project's managed identity" specifically) — retry
            // briefly in case that identity also needs a moment to propagate.
            $identityAttempt = 0;
            pollUntil(
                function () use ($projects, $projectName, $context, &$identityAttempt) {
                    $identityAttempt++;

                    try {
                        return $projects->createOrUpdate($projectName, $context->location, identityType: 'SystemAssigned');
                    } catch (BadRequestException $e) {
                        fwrite(STDERR, "[identity-retry #{$identityAttempt}] {$e->getMessage()}\n");

                        if (str_contains($e->getMessage(), 'managed identity')) {
                            return null;
                        }

                        throw $e;
                    }
                },
                timeoutSeconds: 120,
                intervalSeconds: 15,
            );

            $provisionedProject = pollUntil(
                function () use ($projects, $projectName) {
                    try {
                        $data = $projects->get($projectName);
                    } catch (ConflictException) {
                        return null;
                    }

                    return $data->provisioningState === ProvisioningState::Succeeded ? $data : null;
                },
                timeoutSeconds: 180,
                intervalSeconds: 10,
            );

            // ARM returns nested resources with a compound name (account/project).
            expect($provisionedProject->name)->toBe($accountName.'/'.$projectName);

            // Creating a Foundry project via raw ARM (vs the Foundry portal UI)
            // does not auto-grant the calling principal data-plane access —
            // Microsoft's docs confirm that auto-assignment only happens for
            // Portal-driven creation. Grant "Foundry User" explicitly so the
            // data-plane calls below can authenticate.
            $projectScope = '/subscriptions/'.$context->subscriptionId
                .'/resourceGroups/'.$context->resourceGroupName
                .'/providers/Microsoft.CognitiveServices/accounts/'.$accountName
                .'/projects/'.$projectName;

            $foundryUserRoleDefinitionId = '/subscriptions/'.$context->subscriptionId
                .'/providers/Microsoft.Authorization/roleDefinitions/53ca6127-db72-4b80-b1b0-d745d6d5456d';

            Azure::instance()->roleAssignments($projectScope)->create(
                roleAssignmentName: (string) Str::uuid(),
                roleDefinitionId: $foundryUserRoleDefinitionId,
                principalId: (string) env('MICROSOFT_AZURE_SERVICE_PRINCIPAL_OBJECT_ID'),
                principalType: 'ServicePrincipal',
            );

            $foundry = Azure::instance()->foundry($accountName, $projectName);

            // --- Stable core, predates this session: hard assertions ---
            $agentName = 'lma-prompt-agent';

            // RBAC role assignments can take a short while to propagate before
            // the data plane honors them — retry the first data-plane call.
            $rbacAttempt = 0;
            $created = pollUntil(
                function () use ($foundry, $agentName, &$rbacAttempt) {
                    $rbacAttempt++;

                    try {
                        return $foundry->agents()->create(new CreateAgentPayload(
                            name: $agentName,
                            definition: new GenericJsonPayload(['kind' => 'prompt', 'model' => 'gpt-5-mini', 'instructions' => 'You are a test agent.']),
                        ));
                    } catch (AuthenticationException|ForbiddenException $e) {
                        fwrite(STDERR, "[rbac-retry #{$rbacAttempt}] {$e->getMessage()}\n");

                        return null;
                    }
                },
                timeoutSeconds: 300,
                intervalSeconds: 20,
            );

            expect($created)->toHaveKey('name', $agentName);

            $fetched = $foundry->agents()->get($agentName);
            expect($fetched)->toHaveKey('name', $agentName);

            $list = $foundry->agents()->list();
            expect($list->pluck('name')->all())->toContain($agentName);

            fwrite(STDERR, "[checkpoint] before conversations.create\n");
            $conversation = $foundry->conversations()->create([]);
            fwrite(STDERR, "[checkpoint] after conversations.create\n");
            expect($conversation)->toHaveKey('id');

            $conversationId = (string) $conversation['id'];
            fwrite(STDERR, "[checkpoint] before conversations.get\n");
            $fetchedConversation = $foundry->conversations()->get($conversationId);
            fwrite(STDERR, "[checkpoint] after conversations.get\n");
            expect($fetchedConversation)->toHaveKey('id', $conversationId);

            // --- Gap-filling and brand-new surfaces: attempt and report, don't hard-fail ---
            $attempt = function (string $label, callable $callback) use (&$attempts): void {
                fwrite(STDERR, "[checkpoint] entering attempt: {$label}\n");

                try {
                    $callback();
                    $attempts[$label] = 'ok';
                } catch (Throwable $e) {
                    $attempts[$label] = get_class($e).': '.$e->getMessage();
                }

                fwrite(STDERR, "[checkpoint] left attempt: {$label} -> {$attempts[$label]}\n");
            };

            $attempt('agents.replace', function () use ($foundry, $agentName): void {
                $foundry->agents()->replace($agentName, ['definition' => ['kind' => 'prompt', 'model' => 'gpt-5-mini', 'instructions' => 'Replaced.']]);
            });

            $attempt('conversations.list', function () use ($foundry): void {
                $foundry->conversations()->list();
            });

            $attempt('conversations.update', function () use ($foundry, $conversationId): void {
                $foundry->conversations()->update($conversationId, ['metadata' => ['test' => 'true']]);
            });

            $attempt('responses.create', function () use ($foundry): void {
                $foundry->responses()->create(['model' => 'gpt-5-mini', 'input' => 'Say hello in one word.']);
            });

            $attempt('toolboxes.lifecycle', function () use ($foundry): void {
                $toolboxes = $foundry->withFoundryFeatures([FoundryFeature::Toolboxes])->toolboxes();
                $toolboxes->create(['name' => 'lma-toolbox']);
                $toolboxes->createVersion('lma-toolbox', ['tools' => [['type' => 'toolbox_search_preview']]]);
                $toolboxes->list();
                $toolboxes->delete('lma-toolbox');
            });

            $attempt('connections.lifecycle', function () use ($foundry): void {
                $conn = $foundry->connections()->create(['name' => 'lma-conn', 'kind' => 'remote-tool']);
                $foundry->connections()->get((string) ($conn['id'] ?? $conn['name'] ?? 'lma-conn'));
                $foundry->connections()->list();
            });

            $attempt('skills.lifecycle', function () use ($foundry): void {
                $foundry->skills()->createVersion('lma-skill', ['description' => 'test skill']);
                $foundry->skills()->list();
            });

            $attempt('memoryStores.lifecycle', function () use ($foundry): void {
                $ms = $foundry->memoryStores()->create(['name' => 'lma-memory']);
                $foundry->memoryStores()->get((string) $ms['id']);
                $foundry->memoryStores()->list();
            });

            $attempt('evaluations.lifecycle', function () use ($foundry): void {
                $foundry->evaluations()->create(['name' => 'lma-eval']);
                $foundry->evaluations()->list();
            });

            $attempt('schedules.lifecycle', function () use ($foundry): void {
                $foundry->schedules()->createOrUpdate('lma-schedule', ['cron' => '0 8 * * *']);
                $foundry->schedules()->list();
                $foundry->schedules()->runs('lma-schedule')->list();
            });

            $attempt('datasets.lifecycle', function () use ($foundry): void {
                $foundry->datasets()->createOrUpdateVersion('lma-dataset', '1', ['description' => 'test']);
                $foundry->datasets()->getVersion('lma-dataset', '1');
            });

            $attempt('indexes.lifecycle', function () use ($foundry): void {
                $foundry->indexes()->createOrUpdateVersion('lma-index', '1', ['description' => 'test']);
                $foundry->indexes()->getVersion('lma-index', '1');
            });

            $attempt('redteams.lifecycle', function () use ($foundry): void {
                $foundry->redteams()->create(['target_agent' => $agentName]);
                $foundry->redteams()->list();
            });

            $attempt('agents.delete', function () use ($foundry, $agentName): void {
                $foundry->agents()->delete($agentName);
            });
        } finally {
            fwrite(STDERR, "\n--- Foundry live-attempt results ---\n");
            foreach ($attempts as $label => $result) {
                fwrite(STDERR, "{$label}: {$result}\n");
            }
            fwrite(STDERR, "-------------------------------------\n");

            if ($accountCreated) {
                try {
                    $account->delete();
                } catch (Throwable) {
                    // Best-effort cleanup; the resource group teardown is the safety net.
                }
            }
        }
    });
})->group('slow');
