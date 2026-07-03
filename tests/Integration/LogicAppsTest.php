<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

it('creates, runs, and inspects a consumption Logic Apps workflow', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $workflowName = 'lma-wf-'.Str::lower(Str::random(8));

        $definition = [
            '$schema' => 'https://schema.management.azure.com/providers/Microsoft.Logic/schemas/2016-06-01/workflowdefinition.json#',
            'contentVersion' => '1.0.0.0',
            'triggers' => [
                'manual' => [
                    'type' => 'Request',
                    'kind' => 'Http',
                ],
            ],
            'actions' => [
                'Response' => [
                    'type' => 'Response',
                    'kind' => 'Http',
                    'inputs' => [
                        'statusCode' => 200,
                    ],
                ],
            ],
        ];

        $providers = Azure::instance()->resourceProviders($context->subscriptionId);

        if (! $providers->get('Microsoft.Logic')->isRegistered()) {
            $providers->register('Microsoft.Logic');
            $providers->awaitRegistered('Microsoft.Logic', 300, 10);
        }

        $workflows = Azure::instance()->logicWorkflows($context->subscriptionId, $context->resourceGroupName);
        $workflow = $workflows->workflow($workflowName);

        try {
            $workflows->createOrUpdate($workflowName, $context->location, $definition);

            $created = pollUntil(
                function () use ($workflow) {
                    $data = $workflow->get();

                    return $data->provisioningState === 'Succeeded' ? $data : null;
                },
                timeoutSeconds: 120,
                intervalSeconds: 5,
            );

            expect($created->state)->toBe('Enabled')
                ->and($created->provisioningState)->toBe('Succeeded');

            $workflow->disable();
            $workflow->enable();

            $callbackUrl = $workflow->listCallbackUrl();

            expect($callbackUrl->value)->not->toBeEmpty();

            $triggers = $workflow->triggers()->list();

            expect($triggers->pluck('name'))->toContain('manual');

            $workflow->triggers()->trigger('manual')->run();

            $runs = pollUntil(
                function () use ($workflow) {
                    $list = $workflow->runs()->list();

                    return $list->isNotEmpty() ? $list : null;
                },
                timeoutSeconds: 60,
                intervalSeconds: 5,
            );

            $runName = $runs->first()->name;

            $run = $workflow->runs()->run($runName)->get();

            expect($run->status)->toBeString()->not->toBeEmpty();

            $actions = $workflow->runs()->run($runName)->actions()->list();

            expect($actions)->toBeInstanceOf(Collection::class);
        } finally {
            try {
                $workflow->delete();
            } catch (Throwable) {
                // Best-effort cleanup; the resource group teardown is the safety net.
            }
        }
    });
});
