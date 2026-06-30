<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Deployments;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateDeployment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  array<string, mixed>  $template
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $deploymentName,
        public readonly array $template,
        public readonly array $parameters = [],
        public readonly DeploymentMode $mode = DeploymentMode::Incremental,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourcegroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Resources/deployments/'.$this->deploymentName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DEPLOYMENTS];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'properties' => [
                'mode' => $this->mode->value,
                'template' => $this->template,
                'parameters' => $this->parameters,
            ],
        ];
    }
}
