<?php

use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

afterEach(function (): void {
    MockClient::destroyGlobal();
});

it('fetches an access token from the OAuth endpoint', function (): void {
    MockClient::global([
        ClientCredentialsTokenRequest::class => MockResponse::make(body: accessTokenResponseFixture()),
    ]);

    $token = (new ClientCredentialsTokenFetcher)->fetch(testConnectionConfig(), TokenAudience::Arm);

    expect($token->accessToken)->toBe('eyJ.test.token')
        ->and($token->expiresIn)->toBe(3600);
});

it('throws when oauth token exchange fails', function (): void {
    MockClient::global([
        ClientCredentialsTokenRequest::class => MockResponse::make(
            body: ['error' => 'invalid_client', 'error_description' => 'Bad credentials'],
            status: 401,
        ),
    ]);

    expect(fn () => (new ClientCredentialsTokenFetcher)->fetch(testConnectionConfig(), TokenAudience::Graph))
        ->toThrow(RuntimeException::class, 'Bad credentials');
});

it('throws when oauth token exchange fails without json body', function (): void {
    MockClient::global([
        ClientCredentialsTokenRequest::class => MockResponse::make(body: '', status: 503),
    ]);

    expect(fn () => (new ClientCredentialsTokenFetcher)->fetch(testConnectionConfig(), TokenAudience::Arm))
        ->toThrow(RuntimeException::class, 'Azure OAuth token request failed with HTTP 503');
});

it('uses kudu host scope when audience is kudu', function (): void {
    MockClient::global([
        ClientCredentialsTokenRequest::class => MockResponse::make(body: accessTokenResponseFixture()),
    ]);

    $token = (new ClientCredentialsTokenFetcher)->fetch(
        testConnectionConfig(),
        TokenAudience::Kudu,
        'my-app.scm.azurewebsites.net',
    );

    expect($token->accessToken)->toBe('eyJ.test.token');
});
