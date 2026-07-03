<?php

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\GetWorkflowStatus;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RespondToWorkflow;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunWorkflow;
use Saloon\Http\Request;

dataset('function runtime request endpoints', [
    'RunWorkflow' => [
        fn () => new RunWorkflow('FlowRunner', new GenericJsonPayload(['input' => 'test'])),
        '/api/workflows/FlowRunner/run',
    ],
    'GetWorkflowStatus' => [
        fn () => new GetWorkflowStatus('FlowRunner', 'run-1'),
        '/api/workflows/FlowRunner/status/run-1',
    ],
    'RespondToWorkflow' => [
        fn () => new RespondToWorkflow('FlowRunner', 'run-1', new GenericJsonPayload(['input' => true])),
        '/api/workflows/FlowRunner/respond/run-1',
    ],
]);

it('resolves function runtime request endpoints', function (callable $factory, string $endpoint): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint);
})->with('function runtime request endpoints');

it('passes workflow payload through run workflow body', function (): void {
    $request = new RunWorkflow('FlowRunner', new GenericJsonPayload(['input' => ['id' => 1]]));

    expect($request->body()->all())
        ->toBe(['input' => ['id' => 1]]);
});
