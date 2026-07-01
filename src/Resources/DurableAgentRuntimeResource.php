<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunDurableAgent;

final class DurableAgentRuntimeResource extends FunctionRuntimeScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function run(string $agentName, array $body): array
    {
        $response = $this->dispatchFunctionRuntime(new RunDurableAgent($agentName, new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }
}
