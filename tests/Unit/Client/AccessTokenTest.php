<?php

use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

afterEach(function (): void {
    MockClient::destroyGlobal();
});

it('hands back a cached bearer token for a data-plane audience', function (): void {
    $client = clientWithSeededToken();

    expect($client->accessToken(TokenAudience::CognitiveServicesDataPlane))->toBe('seeded-access-token');
});

it('fetches a token when the cache holds none for that audience', function (): void {
    MockClient::global([
        ClientCredentialsTokenRequest::class => MockResponse::make(body: accessTokenResponseFixture()),
    ]);

    // Sql is deliberately absent from clientWithSeededToken()'s seeded
    // audiences, so this call has to go through the fetch closure.
    $client = clientWithSeededToken();

    expect($client->accessToken(TokenAudience::Sql))->toBe('eyJ.test.token');
});

it('scopes a host-specific audience to that host', function (): void {
    $client = clientWithSeededToken(kuduAppName: 'my-func');

    expect($client->accessToken(TokenAudience::Kudu, 'my-func.scm.azurewebsites.net'))
        ->toBe('seeded-access-token');
});
