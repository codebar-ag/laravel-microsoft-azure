<?php

use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\CreateConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\DeleteConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\GetConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\ListConnections;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\CreateSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\DeleteSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\GetSkill;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\GetSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\ListSkills;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\UpdateSkill;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CallToolboxMcpTool;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CreateToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CreateToolboxVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\DeleteToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\GetToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxes;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxMcpTools;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\UpdateToolbox;
use Saloon\Http\Faking\MockResponse;

it('covers the toolboxes resource gateway lifecycle', function (): void {
    $client = clientWithFoundryMock([
        CreateToolbox::class => MockResponse::make(body: ['name' => 'docuware-tools']),
        CreateToolboxVersion::class => MockResponse::make(body: ['version' => '1']),
        ListToolboxes::class => MockResponse::make(body: ['data' => [['name' => 'docuware-tools']]]),
        UpdateToolbox::class => MockResponse::make(body: ['name' => 'docuware-tools', 'default_version' => '1']),
        DeleteToolbox::class => MockResponse::make(status: 204),
        ListToolboxMcpTools::class => MockResponse::make(body: ['result' => ['tools' => [['name' => 'search']]]]),
        CallToolboxMcpTool::class => MockResponse::make(body: ['result' => ['content' => []]]),
    ]);

    $toolboxes = $client->foundry('my-foundry', 'default')->toolboxes();

    expect($toolboxes->create(['name' => 'docuware-tools']))->toHaveKey('name', 'docuware-tools')
        ->and($toolboxes->createVersion('docuware-tools', ['tools' => []]))->toHaveKey('version', '1')
        ->and($toolboxes->list())->toHaveCount(1)
        ->and($toolboxes->setDefaultVersion('docuware-tools', '1'))->toHaveKey('default_version', '1')
        ->and($toolboxes->listMcpTools('docuware-tools', '1'))->toHaveKey('result')
        ->and($toolboxes->callMcpTool('docuware-tools', '1', 'search', ['q' => 'x']))->toHaveKey('result');

    $toolboxes->delete('docuware-tools');
});

it('applies Foundry-Features to every toolboxes request, including reads', function (): void {
    $client = clientWithFoundryMock([
        CreateToolbox::class => function ($request) {
            expect($request->headers()->get('Foundry-Features'))->toBe('Toolboxes=V1Preview');

            return MockResponse::make(body: ['name' => 'docuware-tools']);
        },
        GetToolbox::class => function ($request) {
            expect($request->headers()->get('Foundry-Features'))->toBe('Toolboxes=V1Preview');

            return MockResponse::make(body: ['name' => 'docuware-tools']);
        },
    ]);

    $toolboxes = $client->foundry('my-foundry', 'default')
        ->withFoundryFeatures([FoundryFeature::Toolboxes])
        ->toolboxes();

    $toolboxes->create(['name' => 'docuware-tools']);
    $toolboxes->get('docuware-tools');
});

it('covers the connections resource gateway lifecycle', function (): void {
    $client = clientWithFoundryMock([
        CreateConnection::class => MockResponse::make(body: ['id' => 'conn-1']),
        ListConnections::class => MockResponse::make(body: ['data' => [['id' => 'conn-1']]]),
        GetConnection::class => MockResponse::make(body: ['id' => 'conn-1']),
        DeleteConnection::class => MockResponse::make(status: 204),
    ]);

    $connections = $client->foundry('my-foundry', 'default')->connections();

    expect($connections->create(['name' => 'docuware-mcp-conn']))->toHaveKey('id', 'conn-1')
        ->and($connections->list())->toHaveCount(1)
        ->and($connections->get('conn-1'))->toHaveKey('id', 'conn-1');

    $connections->delete('conn-1');
});

it('covers the skills resource gateway lifecycle', function (): void {
    $client = clientWithFoundryMock([
        CreateSkillVersion::class => MockResponse::make(body: ['version' => '1']),
        ListSkills::class => MockResponse::make(body: ['data' => [['name' => 'doc-review']]]),
        GetSkill::class => MockResponse::make(body: ['name' => 'doc-review']),
        GetSkillVersion::class => MockResponse::make(body: ['name' => 'doc-review', 'version' => '1']),
        UpdateSkill::class => MockResponse::make(body: ['name' => 'doc-review', 'default_version' => '1']),
        DeleteSkillVersion::class => MockResponse::make(status: 204),
    ]);

    $skills = $client->foundry('my-foundry', 'default')->skills();

    expect($skills->createVersion('doc-review', ['description' => 'v1']))->toHaveKey('version', '1')
        ->and($skills->list())->toHaveCount(1)
        ->and($skills->get('doc-review'))->toHaveKey('name', 'doc-review')
        ->and($skills->getVersion('doc-review', '1'))->toHaveKey('version', '1')
        ->and($skills->setDefaultVersion('doc-review', '1'))->toHaveKey('default_version', '1');

    $skills->deleteVersion('doc-review', '1');
});
