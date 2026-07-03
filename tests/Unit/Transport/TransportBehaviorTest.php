<?php

use CodebarAg\MicrosoftAzure\Exceptions\BadRequestException;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy;
use CodebarAg\MicrosoftAzure\Transport\OAuthConnector;
use Saloon\Http\Faking\MockResponse;

it('does not retry non-idempotent client errors', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(body: ['error' => ['message' => 'bad']], status: 400),
    ]);

    expect(fn () => $client->resourceGroups('sub-1')->get('rg-test'))
        ->toThrow(BadRequestException::class);
});

it('resolves oauth connector base url', function (): void {
    expect((new OAuthConnector)->resolveBaseUrl())->toBe('https://login.microsoftonline.com');
});

it('throws when zip deploy file cannot be opened', function (): void {
    expect(fn () => (new ZipDeploy('/definitely/missing.zip'))->body()->all())
        ->toThrow(RuntimeException::class);
});

it('throws when openReadableStream cannot open the file', function (): void {
    $request = new ZipDeploy(__FILE__);
    $method = new ReflectionMethod($request, 'openReadableStream');

    expect(fn () => $method->invoke($request, '/path/does/not/exist/'.uniqid('', true).'.zip'))
        ->toThrow(RuntimeException::class, 'could not be opened');
});

it('opens readable zip files for upload', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'zip');
    file_put_contents($path, 'PK'.str_repeat("\0", 20));

    $request = new ZipDeploy($path);
    $method = new ReflectionMethod($request, 'openReadableStream');
    $method->setAccessible(true);

    $handle = $method->invoke($request, $path);

    expect(is_resource($handle))->toBeTrue();

    fclose($handle);
    @unlink($path);
});
