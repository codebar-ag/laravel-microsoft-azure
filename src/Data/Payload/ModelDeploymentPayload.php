<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class ModelDeploymentPayload extends AzurePayload
{
    public function __construct(
        public readonly string $modelFormat,
        public readonly string $modelName,
        public readonly ?string $modelVersion = null,
        public readonly string $skuName = 'GlobalStandard',
        public readonly int $skuCapacity = 1,
    ) {}

    public function toAzureBody(): array
    {
        $model = [
            'format' => $this->modelFormat,
            'name' => $this->modelName,
        ];

        if ($this->modelVersion !== null) {
            $model['version'] = $this->modelVersion;
        }

        return [
            'sku' => [
                'name' => $this->skuName,
                'capacity' => $this->skuCapacity,
            ],
            'properties' => [
                'model' => $model,
            ],
        ];
    }
}
