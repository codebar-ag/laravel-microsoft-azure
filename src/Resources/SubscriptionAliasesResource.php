<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\DeleteSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\ListSubscriptionAliases;
use Illuminate\Support\Collection;

final class SubscriptionAliasesResource extends Resource
{
    /**
     * Create a new subscription under a billing scope (MCA, EA enrollment account, invoice section).
     *
     * Poll {@see get()} until {@see SubscriptionAliasData::$provisioningState} is terminal.
     *
     * @param  array<string, mixed>|null  $additionalProperties
     * @param  array<string, string>|null  $tags
     */
    public function createOrUpdate(
        string $aliasName,
        string $billingScope,
        string $displayName,
        SubscriptionWorkload $workload = SubscriptionWorkload::Production,
        ?string $subscriptionId = null,
        ?array $additionalProperties = null,
        ?array $tags = null,
    ): SubscriptionAliasData {
        $response = $this->sendArm(new CreateOrUpdateSubscriptionAlias(
            $aliasName,
            $billingScope,
            $displayName,
            $workload,
            $subscriptionId,
            $additionalProperties,
            $tags,
        ));

        return SubscriptionAliasData::fromAzure($response->json());
    }

    public function get(string $aliasName): SubscriptionAliasData
    {
        $response = $this->sendArm(new GetSubscriptionAlias($aliasName));

        return SubscriptionAliasData::fromAzure($response->json());
    }

    public function delete(string $aliasName): void
    {
        $this->sendArm(new DeleteSubscriptionAlias($aliasName));
    }

    /**
     * @return Collection<int, SubscriptionAliasData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListSubscriptionAliases);

        return $this->mapList($response, 'value', fn (array $item) => SubscriptionAliasData::fromAzure($item));
    }
}
