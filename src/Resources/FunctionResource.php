<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\FunctionData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\GetFunction;

final class FunctionResource extends Resource
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

    public function get(): FunctionData
    {
        $response = $this->sendArm(new GetFunction(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $this->functionName,
        ));

        return FunctionData::fromAzure($this->jsonArray($response));
    }

    public function keys(): FunctionKeysResource
    {
        return new FunctionKeysResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $this->functionName,
        );
    }
}
