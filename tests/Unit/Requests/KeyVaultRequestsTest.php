<?php

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\ListSecrets;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret;

it('resolves key vault secret endpoints', function (): void {
    expect((new ListSecrets)->resolveEndpoint())->toBe('/secrets')
        ->and((new GetSecret('webhook-token'))->resolveEndpoint())->toBe('/secrets/webhook-token')
        ->and((new GetSecret('webhook-token', 'abc123'))->resolveEndpoint())->toBe('/secrets/webhook-token/abc123')
        ->and((new DeleteSecret('webhook-token'))->resolveEndpoint())->toBe('/secrets/webhook-token');
});

it('includes key vault api-version on secret requests', function (): void {
    expect((new SetSecret('name', 'value'))->query()->all())
        ->toBe(['api-version' => ApiVersion::KEY_VAULT]);
});

it('builds set secret body with optional attributes', function (): void {
    $request = new SetSecret(
        secretName: 'webhook-token',
        value: 'secret-value',
        attributes: ['contentType' => 'text/plain'],
    );

    expect($request->body()->all())
        ->toMatchArray([
            'value' => 'secret-value',
            'contentType' => 'text/plain',
        ]);
});
