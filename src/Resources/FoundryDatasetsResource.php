<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\CreateOrUpdateDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\DeleteDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\GetDatasetVersion;

final class FoundryDatasetsResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function createOrUpdateVersion(string $datasetName, string $version, array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateOrUpdateDatasetVersion($datasetName, $version, $payload));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function getVersion(string $datasetName, string $version): array
    {
        $response = $this->dispatchFoundry(new GetDatasetVersion($datasetName, $version));

        return $this->jsonArray($response);
    }

    public function deleteVersion(string $datasetName, string $version): void
    {
        $this->dispatchFoundry(new DeleteDatasetVersion($datasetName, $version));
    }
}
