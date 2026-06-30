<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\GetWorkflowStatus;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RespondToWorkflow;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunWorkflow;

final class WorkflowRuntimeResource extends FunctionRuntimeScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function run(string $workflowName, array $body): array
    {
        $response = $this->dispatchFunctionRuntime(new RunWorkflow($workflowName, new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function status(string $workflowName, string $runId): array
    {
        $response = $this->dispatchFunctionRuntime(new GetWorkflowStatus($workflowName, $runId));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function respond(string $workflowName, string $runId, array $body): array
    {
        $response = $this->dispatchFunctionRuntime(new RespondToWorkflow(
            $workflowName,
            $runId,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
