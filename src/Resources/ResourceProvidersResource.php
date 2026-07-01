<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceProviderData;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\GetResourceProvider;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\ListResourceProviders;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\RegisterResourceProvider;
use Illuminate\Support\Collection;

final class ResourceProvidersResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, ResourceProviderData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListResourceProviders($this->subscriptionId));

        return $this->mapList($response, 'value', fn (array $item) => ResourceProviderData::fromAzure($item));
    }

    public function get(string $namespace): ResourceProviderData
    {
        $response = $this->sendArm(new GetResourceProvider($this->subscriptionId, $namespace));

        return ResourceProviderData::fromAzure($this->jsonArray($response));
    }

    public function register(string $namespace): ResourceProviderData
    {
        $response = $this->sendArm(new RegisterResourceProvider($this->subscriptionId, $namespace));

        return ResourceProviderData::fromAzure($this->jsonArray($response));
    }

    public function awaitRegistered(
        string $namespace,
        int $timeoutSeconds = 600,
        int $intervalSeconds = 5,
    ): ResourceProviderData {
        $deadline = time() + $timeoutSeconds;

        do {
            $provider = $this->get($namespace);

            if ($provider->isRegistered()) {
                return $provider;
            }

            if (! $provider->isRegistering()) {
                throw new LongRunningOperationException(
                    "Resource provider [{$namespace}] finished in state [{$provider->registrationState}].",
                    null,
                    $this->client->name(),
                );
            }

            sleep($intervalSeconds);
        } while (time() <= $deadline);

        throw new LongRunningOperationException(
            "Resource provider [{$namespace}] did not register within {$timeoutSeconds}s.",
            null,
            $this->client->name(),
        );
    }
}
