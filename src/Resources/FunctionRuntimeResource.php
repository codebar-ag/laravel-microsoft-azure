<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class FunctionRuntimeResource extends FunctionRuntimeScopedResource
{
    public function agents(): DurableAgentRuntimeResource
    {
        return new DurableAgentRuntimeResource($this->client, $this->appName, $this->hostKey);
    }

    public function workflows(): WorkflowRuntimeResource
    {
        return new WorkflowRuntimeResource($this->client, $this->appName, $this->hostKey);
    }
}
