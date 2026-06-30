<?php

use CodebarAg\MicrosoftAzure\Data\Payload\ClientCredentialsTokenPayload;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest;
use CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus;
use CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy;
use CodebarAg\MicrosoftAzure\Transport\OAuthConnector;

it('builds oauth client credentials token request', function (): void {
    $payload = new ClientCredentialsTokenPayload(
        clientId: 'client-id',
        clientSecret: 'client-secret',
        scope: 'https://management.azure.com/.default',
    );

    $request = new ClientCredentialsTokenRequest(
        tenantId: 'tenant-id',
        payload: $payload,
    );

    expect($payload->toAzureBody())->toMatchArray([
        'grant_type' => 'client_credentials',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'scope' => 'https://management.azure.com/.default',
    ])
        ->and((new OAuthConnector)->resolveBaseUrl())->toBe('https://login.microsoftonline.com')
        ->and($request->resolveEndpoint())->toBe('/tenant-id/oauth2/v2.0/token')
        ->and($request->body()->all())->toMatchArray([
            'grant_type' => 'client_credentials',
            'client_id' => 'client-id',
            'client_secret' => 'client-secret',
            'scope' => 'https://management.azure.com/.default',
        ]);
});

it('maps kudu token audience scope to scm host', function (): void {
    expect(TokenAudience::Kudu->scope('my-func.scm.azurewebsites.net'))
        ->toBe('https://my-func.scm.azurewebsites.net/.default');
});

it('throws when zip deploy file is not readable', function (): void {
    $request = new ZipDeploy('/path/does/not/exist.zip');

    expect(fn () => $request->body()->all())
        ->toThrow(RuntimeException::class, 'not readable');
});

it('resolves kudu zip deploy endpoint', function (): void {
    expect((new ZipDeploy(__FILE__))->resolveEndpoint())->toBe('/api/zipdeploy');
});

it('resolves kudu deployment status with id', function (): void {
    expect((new GetDeploymentStatus('latest'))->resolveEndpoint())->toBe('/api/deployments/latest');
});
