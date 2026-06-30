<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Arm\CanceledSubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\CancelSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\ListSubscriptions;
use Illuminate\Support\Collection;

final class SubscriptionsResource extends Resource
{
    /**
     * @return Collection<int, SubscriptionData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListSubscriptions);

        return $this->mapList($response, 'value', fn (array $item) => SubscriptionData::fromAzure($item));
    }

    public function get(string $subscriptionId): SubscriptionData
    {
        $response = $this->sendArm(new GetSubscription($subscriptionId));

        return SubscriptionData::fromAzure($response->json());
    }

    public function cancel(string $subscriptionId): CanceledSubscriptionData
    {
        $response = $this->sendArm(new CancelSubscription($subscriptionId));

        return CanceledSubscriptionData::fromAzure($response->json());
    }
}
