<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;

final class ServerFarmsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    public function plan(string $planName): ServerFarmResource
    {
        return new ServerFarmResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $planName,
        );
    }
}
