<?php

use CodebarAg\MicrosoftAzure\Exceptions\AuthenticationException;
use CodebarAg\MicrosoftAzure\Exceptions\BadRequestException;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Exceptions\MicrosoftAzureException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;
use CodebarAg\MicrosoftAzure\Exceptions\RateLimitException;
use CodebarAg\MicrosoftAzure\Exceptions\RequestException;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use Saloon\Http\Faking\MockResponse;

dataset('http error statuses', [
    'bad request' => [400, BadRequestException::class],
    'unauthorized' => [401, AuthenticationException::class],
    'forbidden' => [403, ForbiddenException::class],
    'not found' => [404, NotFoundException::class],
    'rate limited' => [429, RateLimitException::class],
    'client error' => [418, RequestException::class],
    'server error' => [503, MicrosoftAzureException::class],
]);

it('maps non-success responses to typed azure exceptions', function (int $status, string $exceptionClass): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(
            body: ['error' => ['message' => 'failure']],
            status: $status,
        ),
    ]);

    expect(fn () => $client->resourceGroups('sub-1')->get('rg-test'))
        ->toThrow($exceptionClass);
})->with('http error statuses');

it('includes azure error context on exceptions', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(
            body: ['message' => 'Resource group not found'],
            status: 404,
            headers: ['x-ms-request-id' => 'req-123'],
        ),
    ]);

    try {
        $client->resourceGroups('sub-1')->get('rg-test');
    } catch (NotFoundException $exception) {
        expect($exception->statusCode)->toBe(404)
            ->and($exception->azureMessage)->toBe('Resource group not found')
            ->and($exception->requestId)->toBe('req-123')
            ->and($exception->context())->toMatchArray([
                'status' => 404,
                'azure_message' => 'Resource group not found',
                'request_id' => 'req-123',
            ]);

        return;
    }

    test()->fail('Expected NotFoundException was not thrown.');
});
