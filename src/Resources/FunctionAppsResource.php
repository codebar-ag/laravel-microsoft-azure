<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\WebSiteData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\ListSitesByResourceGroup;
use Illuminate\Support\Collection;

final class FunctionAppsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, WebSiteData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListSitesByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroupName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => WebSiteData::fromAzure($item));
    }

    public function app(string $appName): FunctionAppResource
    {
        return new FunctionAppResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $appName,
        );
    }
}
