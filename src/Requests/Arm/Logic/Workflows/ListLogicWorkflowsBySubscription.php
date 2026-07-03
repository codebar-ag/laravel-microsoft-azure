<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows;

use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\LogicRequest;
use Saloon\Enums\Method;

final class ListLogicWorkflowsBySubscription extends LogicRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.Logic/workflows';
    }
}
