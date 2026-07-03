<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Payload\AppSettingsPayload;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListApplicationSettings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListConnectionStrings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\UpdateApplicationSettings;

final class FunctionAppSettingsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $appName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return array<string, mixed>
     */
    public function list(): array
    {
        $response = $this->sendArm(new ListApplicationSettings(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return Field::mixedArray($this->jsonArray($response), 'properties');
    }

    /**
     * @param  array<string, string|null>  $properties
     * @return array<string, mixed>
     */
    public function update(array $properties): array
    {
        $response = $this->sendArm(new UpdateApplicationSettings(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            new AppSettingsPayload($properties),
        ));

        return Field::mixedArray($this->jsonArray($response), 'properties');
    }

    /**
     * @return array<string, mixed>
     */
    public function listConnectionStrings(): array
    {
        $response = $this->sendArm(new ListConnectionStrings(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return $this->jsonArray($response);
    }
}
