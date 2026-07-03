<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ApimSubscriptionKeysData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\ListApimSubscriptionSecrets;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\RegenerateApimSubscriptionPrimaryKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\RegenerateApimSubscriptionSecondaryKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\UpdateApimSubscriptionState;

final class ApiManagementSubscriptionResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $serviceName,
        private readonly string $apimSubscriptionId,
    ) {
        parent::__construct($client);
    }

    public function regeneratePrimaryKey(): void
    {
        $this->sendArm(new RegenerateApimSubscriptionPrimaryKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $this->apimSubscriptionId,
        ));
    }

    public function regenerateSecondaryKey(): void
    {
        $this->sendArm(new RegenerateApimSubscriptionSecondaryKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $this->apimSubscriptionId,
        ));
    }

    public function revoke(): void
    {
        $this->sendArm(new UpdateApimSubscriptionState(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $this->apimSubscriptionId,
            new GenericJsonPayload(['properties' => ['state' => 'suspended']]),
        ));
    }

    public function listSecrets(): ApimSubscriptionKeysData
    {
        $response = $this->sendArm(new ListApimSubscriptionSecrets(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $this->apimSubscriptionId,
        ));

        return ApimSubscriptionKeysData::fromAzure($this->jsonArray($response));
    }
}
