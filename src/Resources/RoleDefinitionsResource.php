<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\RoleDefinitionData;
use CodebarAg\MicrosoftAzure\Requests\Arm\RoleDefinitions\ListRoleDefinitions;
use Illuminate\Support\Collection;
use RuntimeException;

final class RoleDefinitionsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, RoleDefinitionData>
     */
    public function list(?string $filter = null): Collection
    {
        $response = $this->sendArm(new ListRoleDefinitions($this->subscriptionId, $filter));

        return $this->mapList($response, 'value', fn (array $item) => RoleDefinitionData::fromAzure($item));
    }

    public function findByName(string $roleName): RoleDefinitionData
    {
        $filter = "roleName eq '{$roleName}'";
        $definitions = $this->list($filter);
        $match = $definitions->first();

        if ($match === null) {
            throw new RuntimeException("Azure role [{$roleName}] was not found for subscription [{$this->subscriptionId}].");
        }

        return $match;
    }
}
