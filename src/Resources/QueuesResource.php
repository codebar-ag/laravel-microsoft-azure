<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\QueueData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateQueue;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\DeleteQueue;

final class QueuesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    /**
     * @param  array<string, string>  $metadata
     */
    public function createOrUpdate(string $queueName, array $metadata = []): QueueData
    {
        $response = $this->sendArm(new CreateOrUpdateQueue(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $queueName,
            $metadata,
        ));

        return QueueData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $queueName): void
    {
        $this->sendArm(new DeleteQueue(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $queueName,
        ));
    }
}
