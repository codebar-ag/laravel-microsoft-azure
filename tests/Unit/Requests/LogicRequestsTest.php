<?php

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\LogicWorkflowPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
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
use Saloon\Http\Request;

function logicWorkflowPayload(): LogicWorkflowPayload
{
    return new LogicWorkflowPayload('westeurope', ['triggers' => []]);
}

dataset('logic request endpoints', function (): array {
    $workflow = '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows/wf1';

    return [
        'CreateOrUpdateLogicWorkflow' => [
            fn () => new CreateOrUpdateLogicWorkflow('sub-1', 'rg-test', 'wf1', logicWorkflowPayload()),
            $workflow,
        ],
        'UpdateLogicWorkflow' => [
            fn () => new UpdateLogicWorkflow('sub-1', 'rg-test', 'wf1', new GenericJsonPayload(['tags' => ['env' => 'test']])),
            $workflow,
        ],
        'GetLogicWorkflow' => [
            fn () => new GetLogicWorkflow('sub-1', 'rg-test', 'wf1'),
            $workflow,
        ],
        'DeleteLogicWorkflow' => [
            fn () => new DeleteLogicWorkflow('sub-1', 'rg-test', 'wf1'),
            $workflow,
        ],
        'ListLogicWorkflowsByResourceGroup' => [
            fn () => new ListLogicWorkflowsByResourceGroup('sub-1', 'rg-test'),
            '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/workflows',
        ],
        'ListLogicWorkflowsBySubscription' => [
            fn () => new ListLogicWorkflowsBySubscription('sub-1'),
            '/subscriptions/sub-1/providers/Microsoft.Logic/workflows',
        ],
        'EnableLogicWorkflow' => [
            fn () => new EnableLogicWorkflow('sub-1', 'rg-test', 'wf1'),
            $workflow.'/enable',
        ],
        'DisableLogicWorkflow' => [
            fn () => new DisableLogicWorkflow('sub-1', 'rg-test', 'wf1'),
            $workflow.'/disable',
        ],
        'ListLogicWorkflowCallbackUrl' => [
            fn () => new ListLogicWorkflowCallbackUrl('sub-1', 'rg-test', 'wf1'),
            $workflow.'/listCallbackUrl',
        ],
        'GenerateUpgradedDefinition' => [
            fn () => new GenerateUpgradedDefinition('sub-1', 'rg-test', 'wf1', new GenericJsonPayload(['targetSchemaVersion' => '2016-06-01'])),
            $workflow.'/generateUpgradedDefinition',
        ],
        'RegenerateLogicWorkflowAccessKey' => [
            fn () => new RegenerateLogicWorkflowAccessKey('sub-1', 'rg-test', 'wf1', new GenericJsonPayload(['keyType' => 'Primary'])),
            $workflow.'/regenerateAccessKey',
        ],
        'ValidateLogicWorkflow' => [
            fn () => new ValidateLogicWorkflow('sub-1', 'rg-test', 'wf1', logicWorkflowPayload()),
            $workflow.'/validate',
        ],
        'ListLogicWorkflowVersions' => [
            fn () => new ListLogicWorkflowVersions('sub-1', 'rg-test', 'wf1'),
            $workflow.'/versions',
        ],
        'GetLogicWorkflowVersion' => [
            fn () => new GetLogicWorkflowVersion('sub-1', 'rg-test', 'wf1', '08585'),
            $workflow.'/versions/08585',
        ],
        'ListLogicWorkflowTriggers' => [
            fn () => new ListLogicWorkflowTriggers('sub-1', 'rg-test', 'wf1'),
            $workflow.'/triggers',
        ],
        'GetLogicWorkflowTrigger' => [
            fn () => new GetLogicWorkflowTrigger('sub-1', 'rg-test', 'wf1', 'manual'),
            $workflow.'/triggers/manual',
        ],
        'RunLogicWorkflowTrigger' => [
            fn () => new RunLogicWorkflowTrigger('sub-1', 'rg-test', 'wf1', 'manual'),
            $workflow.'/triggers/manual/run',
        ],
        'ResetLogicWorkflowTrigger' => [
            fn () => new ResetLogicWorkflowTrigger('sub-1', 'rg-test', 'wf1', 'manual'),
            $workflow.'/triggers/manual/reset',
        ],
        'ListLogicWorkflowTriggerCallbackUrl' => [
            fn () => new ListLogicWorkflowTriggerCallbackUrl('sub-1', 'rg-test', 'wf1', 'manual'),
            $workflow.'/triggers/manual/listCallbackUrl',
        ],
        'GetLogicWorkflowTriggerSchemaJson' => [
            fn () => new GetLogicWorkflowTriggerSchemaJson('sub-1', 'rg-test', 'wf1', 'manual'),
            $workflow.'/triggers/manual/schemas/json',
        ],
        'SetLogicWorkflowTriggerState' => [
            fn () => new SetLogicWorkflowTriggerState('sub-1', 'rg-test', 'wf1', 'manual', new GenericJsonPayload(['source' => ['id' => 'trigger-id']])),
            $workflow.'/triggers/manual/setState',
        ],
        'ListLogicWorkflowTriggerHistories' => [
            fn () => new ListLogicWorkflowTriggerHistories('sub-1', 'rg-test', 'wf1', 'manual'),
            $workflow.'/triggers/manual/histories',
        ],
        'GetLogicWorkflowTriggerHistory' => [
            fn () => new GetLogicWorkflowTriggerHistory('sub-1', 'rg-test', 'wf1', 'manual', 'hist-1'),
            $workflow.'/triggers/manual/histories/hist-1',
        ],
        'ResubmitLogicWorkflowTriggerHistory' => [
            fn () => new ResubmitLogicWorkflowTriggerHistory('sub-1', 'rg-test', 'wf1', 'manual', 'hist-1'),
            $workflow.'/triggers/manual/histories/hist-1/resubmit',
        ],
        'ListLogicWorkflowRuns' => [
            fn () => new ListLogicWorkflowRuns('sub-1', 'rg-test', 'wf1'),
            $workflow.'/runs',
        ],
        'GetLogicWorkflowRun' => [
            fn () => new GetLogicWorkflowRun('sub-1', 'rg-test', 'wf1', 'run-1'),
            $workflow.'/runs/run-1',
        ],
        'CancelLogicWorkflowRun' => [
            fn () => new CancelLogicWorkflowRun('sub-1', 'rg-test', 'wf1', 'run-1'),
            $workflow.'/runs/run-1/cancel',
        ],
        'ListLogicWorkflowRunActions' => [
            fn () => new ListLogicWorkflowRunActions('sub-1', 'rg-test', 'wf1', 'run-1'),
            $workflow.'/runs/run-1/actions',
        ],
        'GetLogicWorkflowRunAction' => [
            fn () => new GetLogicWorkflowRunAction('sub-1', 'rg-test', 'wf1', 'run-1', 'Response'),
            $workflow.'/runs/run-1/actions/Response',
        ],
        'ListLogicWorkflowRunActionExpressionTraces' => [
            fn () => new ListLogicWorkflowRunActionExpressionTraces('sub-1', 'rg-test', 'wf1', 'run-1', 'Response'),
            $workflow.'/runs/run-1/actions/Response/listExpressionTraces',
        ],
    ];
});

it('resolves logic request endpoints and api-version query', function (callable $factory, string $endpoint): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => ApiVersion::ARM_LOGIC]);
})->with('logic request endpoints');

it('builds logic workflow body with all optional fields', function (): void {
    $request = new CreateOrUpdateLogicWorkflow(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        workflowName: 'wf1',
        payload: new LogicWorkflowPayload(
            location: 'westeurope',
            definition: ['triggers' => [], 'actions' => []],
            parameters: ['param1' => ['value' => 'a']],
            state: 'Enabled',
            integrationAccountId: '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/integrationAccounts/ia1',
            tags: ['env' => 'test'],
        ),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'definition' => ['triggers' => [], 'actions' => []],
            'parameters' => ['param1' => ['value' => 'a']],
            'state' => 'Enabled',
            'integrationAccount' => ['id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Logic/integrationAccounts/ia1'],
        ],
        'tags' => ['env' => 'test'],
    ]);
});

it('builds logic workflow body with defaults only', function (): void {
    $request = new CreateOrUpdateLogicWorkflow(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        workflowName: 'wf1',
        payload: new LogicWorkflowPayload('westeurope', ['triggers' => []]),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'definition' => ['triggers' => []],
        ],
    ]);
});

it('builds generic bodies for update, upgrade, regenerate and setState requests', function (): void {
    $update = new UpdateLogicWorkflow('sub-1', 'rg-test', 'wf1', new GenericJsonPayload(['tags' => ['env' => 'test']]));
    $upgrade = new GenerateUpgradedDefinition('sub-1', 'rg-test', 'wf1', new GenericJsonPayload(['targetSchemaVersion' => '2016-06-01']));
    $regenerate = new RegenerateLogicWorkflowAccessKey('sub-1', 'rg-test', 'wf1', new GenericJsonPayload(['keyType' => 'Primary']));
    $setState = new SetLogicWorkflowTriggerState('sub-1', 'rg-test', 'wf1', 'manual', new GenericJsonPayload(['source' => ['id' => 'trigger-id']]));
    $validate = new ValidateLogicWorkflow('sub-1', 'rg-test', 'wf1', logicWorkflowPayload());

    expect($update->body()->all())->toBe(['tags' => ['env' => 'test']])
        ->and($upgrade->body()->all())->toBe(['targetSchemaVersion' => '2016-06-01'])
        ->and($regenerate->body()->all())->toBe(['keyType' => 'Primary'])
        ->and($setState->body()->all())->toBe(['source' => ['id' => 'trigger-id']])
        ->and($validate->body()->all())->toBe(['location' => 'westeurope', 'properties' => ['definition' => ['triggers' => []]]]);
});
