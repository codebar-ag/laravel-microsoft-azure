<?php

use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\MicrosoftAzure\Transport\Auth\SharedKeyAuthenticator;
use CodebarAg\MicrosoftAzure\Transport\StorageQueueConnector;
use Saloon\Http\Auth\TokenAuthenticator;

it('resolves the queue host base url', function (): void {
    $config = testConnectionConfig();
    $tokens = new EncryptedCacheTokenRepository;
    $fetcher = new ClientCredentialsTokenFetcher;

    $connector = new StorageQueueConnector($config, $tokens, $fetcher, 'mystorageacct');

    expect($connector->resolveBaseUrl())->toBe('https://mystorageacct.queue.core.windows.net')
        ->and($connector->defaultHeaders())->toMatchArray([
            'Accept' => 'application/xml',
        ])
        ->and($connector->defaultHeaders())->toHaveKey('x-ms-version');
});

it('uses a shared key authenticator when an account key is given, oauth otherwise', function (): void {
    $config = testConnectionConfig();
    $tokens = new EncryptedCacheTokenRepository;
    $fetcher = new ClientCredentialsTokenFetcher;

    $sharedKeyConnector = new StorageQueueConnector($config, $tokens, $fetcher, 'mystorageacct', 'YWNjb3VudC1rZXk=');
    $sharedKeyAuth = (new ReflectionMethod($sharedKeyConnector, 'defaultAuth'))->invoke($sharedKeyConnector);

    expect($sharedKeyAuth)->toBeInstanceOf(SharedKeyAuthenticator::class);

    clientWithSeededToken();

    $oauthConnector = new StorageQueueConnector($config, $tokens, $fetcher, 'mystorageacct');
    $oauthAuth = (new ReflectionMethod($oauthConnector, 'defaultAuth'))->invoke($oauthConnector);

    expect($oauthAuth)->toBeInstanceOf(TokenAuthenticator::class);
});
