<?php

use CodebarAg\MicrosoftAzure\Data\Arm\LogicCallbackUrlData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunActionData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerHistoryData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowVersionData;
use CodebarAg\MicrosoftAzure\Data\Payload\LogicWorkflowPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\GetLogicWorkflowRunAction;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActionExpressionTraces;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActions;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\CancelLogicWorkflowRun;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\GetLogicWorkflowRun;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\ListLogicWorkflowRuns;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\GetLogicWorkflowTriggerHistory;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ListLogicWorkflowTriggerHistories;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ResubmitLogicWorkflowTriggerHistory;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTrigger;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTriggerSchemaJson;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggerCallbackUrl;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggers;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ResetLogicWorkflowTrigger;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\RunLogicWorkflowTrigger;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\SetLogicWorkflowTriggerState;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\GetLogicWorkflowVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\ListLogicWorkflowVersions;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\CreateOrUpdateLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DeleteLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DisableLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\EnableLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GenerateUpgradedDefinition;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GetLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowCallbackUrl;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsBySubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\RegenerateLogicWorkflowAccessKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\UpdateLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ValidateLogicWorkflow;
use CodebarAg\MicrosoftAzure\Resources\LogicWorkflowsResource;
use Saloon\Http\Faking\MockResponse;

function logicWorkflowFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1',
        'name' => 'wf1',
        'location' => 'westeurope',
        'properties' => [
            'state' => 'Enabled',
            'provisioningState' => 'Succeeded',
            'accessEndpoint' => 'https://prod-01.westeurope.logic.azure.com:443/workflows/abc',
            'createdTime' => '2026-01-01T00:00:00Z',
            'changedTime' => '2026-01-02T00:00:00Z',
            'version' => '08585',
            'definition' => ['triggers' => ['manual' => ['type' => 'Request']]],
            'parameters' => ['param1' => ['value' => 'a']],
        ],
    ];
}

it('lists logic workflows by resource group and subscription', function (): void {
    $client = clientWithArmMock([
        ListLogicWorkflowsByResourceGroup::class => MockResponse::make(body: ['value' => [logicWorkflowFixture()]]),
        ListLogicWorkflowsBySubscription::class => MockResponse::make(body: ['value' => [logicWorkflowFixture(), logicWorkflowFixture()]]),
    ]);

    $gateway = new LogicWorkflowsResource($client, 'sub-1', 'rg-test');

    $byResourceGroup = $gateway->list();
    $bySubscription = $gateway->listBySubscription();

    expect($byResourceGroup)->toHaveCount(1)
        ->and($byResourceGroup->first())->toBeInstanceOf(LogicWorkflowData::class)
        ->and($byResourceGroup->first()?->name)->toBe('wf1')
        ->and($byResourceGroup->first()?->state)->toBe('Enabled')
        ->and($byResourceGroup->first()?->accessEndpoint)->toBe('https://prod-01.westeurope.logic.azure.com:443/workflows/abc')
        ->and($byResourceGroup->first()?->definition)->toHaveKey('triggers')
        ->and($bySubscription)->toHaveCount(2);
});

it('creates, gets, updates and deletes a logic workflow', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateLogicWorkflow::class => MockResponse::make(body: logicWorkflowFixture()),
        GetLogicWorkflow::class => MockResponse::make(body: logicWorkflowFixture()),
        UpdateLogicWorkflow::class => MockResponse::make(body: logicWorkflowFixture()),
        DeleteLogicWorkflow::class => MockResponse::make(body: '', status: 200),
    ]);

    $gateway = new LogicWorkflowsResource($client, 'sub-1', 'rg-test');

    $created = $gateway->createOrUpdate('wf1', 'westeurope', ['triggers' => []], state: 'Enabled', tags: ['env' => 'test']);
    $fetched = $gateway->workflow('wf1')->get();
    $updated = $gateway->workflow('wf1')->update(['tags' => ['env' => 'prod']]);
    $gateway->workflow('wf1')->delete();

    expect($created)->toBeInstanceOf(LogicWorkflowData::class)
        ->and($created->provisioningState)->toBe('Succeeded')
        ->and($fetched->version)->toBe('08585')
        ->and($updated->name)->toBe('wf1');
});

it('enables, disables, validates and manages workflow keys and callback urls', function (): void {
    $client = clientWithArmMock([
        EnableLogicWorkflow::class => MockResponse::make(body: '', status: 200),
        DisableLogicWorkflow::class => MockResponse::make(body: '', status: 200),
        ValidateLogicWorkflow::class => MockResponse::make(body: '', status: 200),
        RegenerateLogicWorkflowAccessKey::class => MockResponse::make(body: '', status: 200),
        ListLogicWorkflowCallbackUrl::class => MockResponse::make(body: [
            'value' => 'https://prod-01.westeurope.logic.azure.com:443/workflows/abc/triggers/manual/paths/invoke?sig=xyz',
            'method' => 'POST',
            'basePath' => 'https://prod-01.westeurope.logic.azure.com/workflows/abc/triggers/manual/paths/invoke',
            'queries' => ['api-version' => '2019-05-01', 'sig' => 'xyz'],
        ]),
        GenerateUpgradedDefinition::class => MockResponse::make(body: ['definition' => ['$schema' => 'https://schema.management.azure.com/...']]),
    ]);

    $workflow = (new LogicWorkflowsResource($client, 'sub-1', 'rg-test'))->workflow('wf1');

    $workflow->enable();
    $workflow->disable();
    $workflow->validate(new LogicWorkflowPayload('westeurope', ['triggers' => []]));
    $workflow->regenerateAccessKey();

    $callback = $workflow->listCallbackUrl();
    $upgraded = $workflow->generateUpgradedDefinition();

    expect($callback)->toBeInstanceOf(LogicCallbackUrlData::class)
        ->and($callback->value)->toContain('sig=xyz')
        ->and($callback->method)->toBe('POST')
        ->and($callback->queries)->toHaveKey('sig')
        ->and($upgraded)->toHaveKey('definition');
});

it('lists and gets workflow versions', function (): void {
    $versionFixture = [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1/versions/08585',
        'name' => '08585',
        'properties' => [
            'state' => 'Enabled',
            'createdTime' => '2026-01-01T00:00:00Z',
            'changedTime' => '2026-01-02T00:00:00Z',
            'definition' => ['triggers' => []],
        ],
    ];

    $client = clientWithArmMock([
        ListLogicWorkflowVersions::class => MockResponse::make(body: ['value' => [$versionFixture]]),
        GetLogicWorkflowVersion::class => MockResponse::make(body: $versionFixture),
    ]);

    $versions = (new LogicWorkflowsResource($client, 'sub-1', 'rg-test'))->workflow('wf1')->versions();

    expect($versions->list())->toHaveCount(1)
        ->and($versions->list()->first())->toBeInstanceOf(LogicWorkflowVersionData::class)
        ->and($versions->get('08585')->name)->toBe('08585')
        ->and($versions->get('08585')->state)->toBe('Enabled');
});

it('lists, gets and operates workflow triggers', function (): void {
    $triggerFixture = [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1/triggers/manual',
        'name' => 'manual',
        'properties' => [
            'state' => 'Enabled',
            'provisioningState' => 'Succeeded',
            'status' => 'Succeeded',
            'lastExecutionTime' => '2026-01-01T00:00:00Z',
            'nextExecutionTime' => '2026-01-02T00:00:00Z',
        ],
    ];

    $client = clientWithArmMock([
        ListLogicWorkflowTriggers::class => MockResponse::make(body: ['value' => [$triggerFixture]]),
        GetLogicWorkflowTrigger::class => MockResponse::make(body: $triggerFixture),
        RunLogicWorkflowTrigger::class => MockResponse::make(body: '', status: 202),
        ResetLogicWorkflowTrigger::class => MockResponse::make(body: '', status: 200),
        SetLogicWorkflowTriggerState::class => MockResponse::make(body: '', status: 200),
        ListLogicWorkflowTriggerCallbackUrl::class => MockResponse::make(body: ['value' => 'https://callback', 'method' => 'POST']),
        GetLogicWorkflowTriggerSchemaJson::class => MockResponse::make(body: ['type' => 'object']),
    ]);

    $triggers = (new LogicWorkflowsResource($client, 'sub-1', 'rg-test'))->workflow('wf1')->triggers();
    $trigger = $triggers->trigger('manual');

    $trigger->run();
    $trigger->reset();
    $trigger->setState('/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1/triggers/manual');

    expect($triggers->list())->toHaveCount(1)
        ->and($triggers->list()->first())->toBeInstanceOf(LogicWorkflowTriggerData::class)
        ->and($trigger->get()->status)->toBe('Succeeded')
        ->and($trigger->get()->lastExecutionTime)->toBe('2026-01-01T00:00:00Z')
        ->and($trigger->listCallbackUrl()->value)->toBe('https://callback')
        ->and($trigger->schemaJson())->toBe(['type' => 'object']);
});

it('lists, gets and resubmits trigger histories', function (): void {
    $historyFixture = [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1/triggers/manual/histories/hist-1',
        'name' => 'hist-1',
        'properties' => [
            'status' => 'Succeeded',
            'code' => 'OK',
            'startTime' => '2026-01-01T00:00:00Z',
            'endTime' => '2026-01-01T00:00:01Z',
            'fired' => true,
            'run' => ['name' => 'run-1'],
        ],
    ];

    $client = clientWithArmMock([
        ListLogicWorkflowTriggerHistories::class => MockResponse::make(body: ['value' => [$historyFixture]]),
        GetLogicWorkflowTriggerHistory::class => MockResponse::make(body: $historyFixture),
        ResubmitLogicWorkflowTriggerHistory::class => MockResponse::make(body: '', status: 202),
    ]);

    $histories = (new LogicWorkflowsResource($client, 'sub-1', 'rg-test'))
        ->workflow('wf1')->triggers()->trigger('manual')->histories();

    $histories->resubmit('hist-1');

    expect($histories->list())->toHaveCount(1)
        ->and($histories->list()->first())->toBeInstanceOf(LogicWorkflowTriggerHistoryData::class)
        ->and($histories->get('hist-1')->fired)->toBeTrue()
        ->and($histories->get('hist-1')->runName)->toBe('run-1');
});

it('lists, gets and cancels workflow runs and reads run actions', function (): void {
    $runFixture = [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1/runs/run-1',
        'name' => 'run-1',
        'properties' => [
            'status' => 'Succeeded',
            'code' => 'OK',
            'startTime' => '2026-01-01T00:00:00Z',
            'endTime' => '2026-01-01T00:00:05Z',
            'trigger' => ['name' => 'manual'],
            'correlation' => ['clientTrackingId' => 'track-1'],
        ],
    ];

    $actionFixture = [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1/runs/run-1/actions/Response',
        'name' => 'Response',
        'properties' => [
            'status' => 'Succeeded',
            'code' => 'OK',
            'startTime' => '2026-01-01T00:00:01Z',
            'endTime' => '2026-01-01T00:00:02Z',
            'trackingId' => 'track-2',
        ],
    ];

    $client = clientWithArmMock([
        ListLogicWorkflowRuns::class => MockResponse::make(body: ['value' => [$runFixture]]),
        GetLogicWorkflowRun::class => MockResponse::make(body: $runFixture),
        CancelLogicWorkflowRun::class => MockResponse::make(body: '', status: 200),
        ListLogicWorkflowRunActions::class => MockResponse::make(body: ['value' => [$actionFixture]]),
        GetLogicWorkflowRunAction::class => MockResponse::make(body: $actionFixture),
        ListLogicWorkflowRunActionExpressionTraces::class => MockResponse::make(body: ['inputs' => [['path' => 'action.inputs', 'value' => 'x']]]),
    ]);

    $runs = (new LogicWorkflowsResource($client, 'sub-1', 'rg-test'))->workflow('wf1')->runs();
    $run = $runs->run('run-1');
    $actions = $run->actions();

    $run->cancel();

    expect($runs->list())->toHaveCount(1)
        ->and($runs->list()->first())->toBeInstanceOf(LogicWorkflowRunData::class)
        ->and($run->get()->triggerName)->toBe('manual')
        ->and($run->get()->clientTrackingId)->toBe('track-1')
        ->and($actions->list())->toHaveCount(1)
        ->and($actions->list()->first())->toBeInstanceOf(LogicWorkflowRunActionData::class)
        ->and($actions->get('Response')->trackingId)->toBe('track-2')
        ->and($actions->expressionTraces('Response'))->toHaveCount(1);
});
