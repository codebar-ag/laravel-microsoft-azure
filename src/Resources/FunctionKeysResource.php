<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\HostKeysData;
use CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateFunctionKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteFunctionKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListFunctionKeys;

final class FunctionKeysResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $appName,
        private readonly string $functionName,
    ) {
        parent::__construct($client);
    }

    public function list(): HostKeysData
    {
        $response = $this->sendArm(new ListFunctionKeys(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $this->functionName,
        ));

        return HostKeysData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(string $keyName, string $value): HostKeysData
    {
        $response = $this->sendArm(new CreateOrUpdateFunctionKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $this->functionName,
            $keyName,
            new FunctionKeyPayload($value),
        ));

        return HostKeysData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $keyName): void
    {
        $this->sendArm(new DeleteFunctionKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $this->functionName,
            $keyName,
        ));
    }
}
