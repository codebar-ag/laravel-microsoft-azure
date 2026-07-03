<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Graph\ServicePrincipalData;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateServicePrincipalPayload;
use CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\CreateServicePrincipal;
use CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\DeleteServicePrincipal;
use CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\ListServicePrincipals;
use Illuminate\Support\Collection;
use RuntimeException;

final class ServicePrincipalsResource extends Resource
{
    public function create(string $appId): ServicePrincipalData
    {
        $response = $this->sendGraph(new CreateServicePrincipal(new CreateServicePrincipalPayload($appId)));

        return ServicePrincipalData::fromAzure($this->jsonArray($response));
    }

    public function findByAppId(string $appId): ?ServicePrincipalData
    {
        $filter = "appId eq '{$appId}'";
        $response = $this->sendGraph(new ListServicePrincipals($filter));
        $items = $this->mapList($response, 'value', fn (array $item) => ServicePrincipalData::fromAzure($item));

        return $items->first();
    }

    public function findByAppIdOrFail(string $appId): ServicePrincipalData
    {
        $principal = $this->findByAppId($appId);

        if ($principal === null) {
            throw new RuntimeException("Service principal for app id [{$appId}] was not found.");
        }

        return $principal;
    }

    /**
     * @return Collection<int, ServicePrincipalData>
     */
    public function list(?string $filter = null): Collection
    {
        $response = $this->sendGraph(new ListServicePrincipals($filter));

        return $this->mapList($response, 'value', fn (array $item) => ServicePrincipalData::fromAzure($item));
    }

    public function delete(string $servicePrincipalObjectId): void
    {
        $this->sendGraph(new DeleteServicePrincipal($servicePrincipalObjectId));
    }
}
