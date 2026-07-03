<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\HostKeysData;
use CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateHostKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteHostKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListHostKeys;

final class FunctionAppHostKeysResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $appName,
    ) {
        parent::__construct($client);
    }

    public function list(): HostKeysData
    {
        $response = $this->sendArm(new ListHostKeys(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return HostKeysData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(string $keyName, string $value): HostKeysData
    {
        $response = $this->sendArm(new CreateOrUpdateHostKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $keyName,
            new FunctionKeyPayload($value),
        ));

        return HostKeysData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $keyName): void
    {
        $this->sendArm(new DeleteHostKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $keyName,
        ));
    }
}
