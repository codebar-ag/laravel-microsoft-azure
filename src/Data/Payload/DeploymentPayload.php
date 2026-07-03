<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;

final class DeploymentPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $template
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public readonly array $template,
        public readonly array $parameters = [],
        public readonly DeploymentMode $mode = DeploymentMode::Incremental,
    ) {}

    public function toAzureBody(): array
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
