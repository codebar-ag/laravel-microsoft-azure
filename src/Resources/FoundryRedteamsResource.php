<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\CreateRedteam;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\GetRedteam;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\ListRedteams;
use Illuminate\Support\Collection;

final class FoundryRedteamsResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function create(array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateRedteam($payload));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListRedteams);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function get(string $redteamName): array
    {
        $response = $this->dispatchFoundry(new GetRedteam($redteamName));

        return $this->jsonArray($response);
    }
}
