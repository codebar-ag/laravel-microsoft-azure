<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class FunctionRuntimeResource extends FunctionRuntimeScopedResource
{
    public function workflows(): WorkflowRuntimeResource
    {
        return new WorkflowRuntimeResource($this->client, $this->appName, $this->hostKey);
    }
}
