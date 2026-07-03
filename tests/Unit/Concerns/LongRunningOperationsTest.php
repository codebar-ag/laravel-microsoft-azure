<?php

use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use Saloon\Http\Faking\MockResponse;

function deploymentBodyWithState(string $state): array
{
    return [
        'id' => '/subscriptions/sub-1/resourcegroups/rg-test/providers/Microsoft.Resources/deployments/tenantflow',
        'name' => 'tenantflow',
        'properties' => [
            'mode' => 'Incremental',
            'provisioningState' => $state,
            'outputs' => [
                'webhookUrl' => ['type' => 'String', 'value' => 'https://func.example/api/IngestWebhook'],
            ],
        ],
    ];
}

it('awaits a deployment until it reaches a terminal succeeded state', function (): void {
    // FIFO sequence: Running, then Succeeded — await() polls GetDeployment twice.
    $client = clientWithArmMock([
        MockResponse::make(body: deploymentBodyWithState('Running')),
        MockResponse::make(body: deploymentBodyWithState('Succeeded')),
    ]);

    $ticks = 0;

    $deployment = $client->deployments('sub-1', 'rg-test')->await(
        'tenantflow',
        timeoutSeconds: 30,
        intervalSeconds: 0,
        onTick: function () use (&$ticks): void {
            $ticks++;
        },
    );

    expect($deployment)->toBeInstanceOf(DeploymentData::class)
        ->and($deployment->provisioningState)->toBe(ProvisioningState::Succeeded)
        ->and($ticks)->toBe(2)
        ->and($deployment->output('webhookUrl'))->toBe('https://func.example/api/IngestWebhook');
});

it('throws when an awaited deployment finishes in a failed state', function (): void {
    $client = clientWithArmMock([
        MockResponse::make(body: deploymentBodyWithState('Failed')),
    ]);

    expect(fn () => $client->deployments('sub-1', 'rg-test')->await(
        'tenantflow',
        timeoutSeconds: 30,
        intervalSeconds: 0,
    ))->toThrow(LongRunningOperationException::class);
});

it('exposes deployment template outputs and a single-output accessor', function (): void {
    $deployment = DeploymentData::fromAzure(deploymentBodyWithState('Succeeded'));

    expect($deployment->outputs)->toHaveKey('webhookUrl')
        ->and($deployment->output('webhookUrl'))->toBe('https://func.example/api/IngestWebhook')
        ->and($deployment->output('missing'))->toBeNull();
});
