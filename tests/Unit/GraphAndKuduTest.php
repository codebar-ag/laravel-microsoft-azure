<?php

use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups;
use CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus;
use CodebarAg\MicrosoftAzure\Tests\Support\MicrosoftAzureFixture;

it('resolves graph list groups endpoint', function (): void {
    $request = new ListGroups(filter: "displayName eq 'Readers'");

    expect($request->resolveEndpoint())->toBe('/groups')
        ->and($request->query()->all())->toHaveKey('$filter');
});

it('resolves kudu deployment status endpoint', function (): void {
    expect((new GetDeploymentStatus('latest'))->resolveEndpoint())->toBe('/api/deployments/latest');
});

it('deserializes deployment mode enum', function (): void {
    expect(DeploymentMode::Incremental->value)->toBe('Incremental');
});

it('replays deployment fixture offline', function (): void {
    $client = clientWithMock([
        GetDeployment::class => new MicrosoftAzureFixture('get-deployment'),
    ]);

    $deployment = $client
        ->deployments('sub-1', 'rg-test')
        ->get('tenantflow');

    expect($deployment->name)->toBe('tenantflow');
});
