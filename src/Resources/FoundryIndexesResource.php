<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\CreateOrUpdateIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\DeleteIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\GetIndexVersion;

final class FoundryIndexesResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function createOrUpdateVersion(string $indexName, string $version, array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateOrUpdateIndexVersion($indexName, $version, $payload));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function getVersion(string $indexName, string $version): array
    {
        $response = $this->dispatchFoundry(new GetIndexVersion($indexName, $version));

        return $this->jsonArray($response);
    }

    public function deleteVersion(string $indexName, string $version): void
    {
        $this->dispatchFoundry(new DeleteIndexVersion($indexName, $version));
    }
}
