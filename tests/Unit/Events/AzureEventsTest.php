<?php

use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Events\AzureResponseReceived;
use CodebarAg\MicrosoftAzure\Events\TokenRefreshed;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use Illuminate\Support\Facades\Event;
use Saloon\Http\Faking\MockResponse;

it('dispatches token refreshed when oauth cache misses', function (): void {
    Event::fake([TokenRefreshed::class]);

    $config = testConnectionConfig();
    (new EncryptedCacheTokenRepository)->accessToken(
        $config,
        TokenAudience::Arm,
        null,
        fn () => new AccessTokenData(
            accessToken: 'token',
            tokenType: 'Bearer',
            expiresIn: 3600,
            expiresAt: now()->addHour(),
        ),
    );

    Event::assertDispatched(TokenRefreshed::class, fn (TokenRefreshed $event) => $event->connection === 'test');
});

it('dispatches azure response received for successful arm calls', function (): void {
    Event::fake([AzureResponseReceived::class]);

    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(body: resourceGroupFixture(), headers: ['x-ms-request-id' => 'req-1']),
    ]);

    $client->resourceGroups('sub-1')->get('rg-test');

    Event::assertDispatched(AzureResponseReceived::class, function (AzureResponseReceived $event): bool {
        return $event->connection === 'test'
            && $event->method === 'GET'
            && $event->status === 200
            && $event->requestId === 'req-1';
    });
});
