<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetLogicWorkflowRun extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workflowName,
        public readonly string $runName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Logic/workflows/'.$this->workflowName
            .'/runs'
            .'/'.$this->runName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_LOGIC];
    }
}
