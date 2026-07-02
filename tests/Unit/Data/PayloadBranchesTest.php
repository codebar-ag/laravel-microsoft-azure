<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\HostedAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RaiConfigPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\StorageAccountPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\UpdateAgentPayload;

it('builds create agent bodies with description and metadata', function (): void {
    $payload = new CreateAgentPayload(
        name: 'my-agent',
        definition: new GenericJsonPayload(['kind' => 'prompt']),
        description: 'An agent',
        metadata: ['team' => 'platform'],
    );

    expect($payload->toAzureBody())->toBe([
        'name' => 'my-agent',
        'definition' => ['kind' => 'prompt'],
        'description' => 'An agent',
        'metadata' => ['team' => 'platform'],
    ]);
});

it('builds update agent bodies with description and metadata', function (): void {
    $payload = new UpdateAgentPayload(
        definition: new GenericJsonPayload(['kind' => 'prompt']),
        description: 'Updated agent',
        metadata: ['team' => 'platform'],
    );

    expect($payload->toAzureBody())->toBe([
        'definition' => ['kind' => 'prompt'],
        'description' => 'Updated agent',
        'metadata' => ['team' => 'platform'],
    ]);
});

it('builds hosted agent definitions with image, env, tools and rai config', function (): void {
    $payload = new HostedAgentDefinitionPayload(
        containerProtocolVersions: [['protocol' => 'responses', 'version' => '1.0']],
        cpu: '1',
        memory: '2Gi',
        image: 'myregistry.azurecr.io/agent:latest',
        environmentVariables: ['LOG_LEVEL' => 'debug'],
        tools: [['type' => 'code_interpreter']],
        raiConfig: new RaiConfigPayload('default-policy'),
    );

    $body = $payload->toAzureBody();

    expect($body['image'])->toBe('myregistry.azurecr.io/agent:latest')
        ->and($body['environment_variables'])->toBe(['LOG_LEVEL' => 'debug'])
        ->and($body['tools'])->toBe([['type' => 'code_interpreter']])
        ->and($body['rai_config'])->toBe(['rai_policy_name' => 'default-policy']);
});

it('builds storage account bodies with custom properties', function (): void {
    $payload = new StorageAccountPayload(
        location: 'westeurope',
        properties: ['minimumTlsVersion' => 'TLS1_2'],
        tags: ['env' => 'test'],
    );

    $body = $payload->toAzureBody();

    expect($body['properties'])->toBe(['minimumTlsVersion' => 'TLS1_2'])
        ->and($body['tags'])->toBe(['env' => 'test']);
});
