<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;

final class VaultResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $vaultName,
    ) {
        parent::__construct($client);
    }

    public function secrets(): SecretsResource
    {
        return new SecretsResource($this->client, $this->vaultName);
    }
}
