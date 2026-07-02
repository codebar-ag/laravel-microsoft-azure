<?php

use CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\ListSecrets;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret;

it('resolves key vault secret endpoints', function (): void {
    expect((new GetSecret('webhook-token'))->resolveEndpoint())->toBe('/secrets/webhook-token')
        ->and((new ListSecrets)->resolveEndpoint())->toBe('/secrets');
});

it('includes key vault api-version on secret requests', function (): void {
    expect((new GetSecret('webhook-token', 'abc123'))->resolveEndpoint())
        ->toBe('/secrets/webhook-token/abc123')
        ->and((new SetSecret('name', new SetSecretPayload('value')))->query()->all())
        ->toBe(['api-version' => ApiVersion::KEY_VAULT]);
});

it('builds set secret body with optional attributes', function (): void {
    $request = new SetSecret(
        secretName: 'webhook-token',
        payload: new SetSecretPayload(
            value: 'secret-value',
            attributes: ['attributes' => ['enabled' => true]],
        ),
    );

    expect($request->body()->all())
        ->toMatchArray([
            'value' => 'secret-value',
            'attributes' => ['enabled' => true],
        ]);
});
