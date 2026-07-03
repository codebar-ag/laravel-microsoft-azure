<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\CreateSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\DeleteSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\GetSkill;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\GetSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\ListSkills;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\UpdateSkill;
use Illuminate\Support\Collection;

final class FoundrySkillsResource extends FoundryScopedResource
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListSkills);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function get(string $skillName): array
    {
        $response = $this->dispatchFoundry(new GetSkill($skillName));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function createVersion(string $skillName, array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateSkillVersion($skillName, $payload));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function getVersion(string $skillName, string $version): array
    {
        $response = $this->dispatchFoundry(new GetSkillVersion($skillName, $version));

        return $this->jsonArray($response);
    }

    public function deleteVersion(string $skillName, string $version): void
    {
        $this->dispatchFoundry(new DeleteSkillVersion($skillName, $version));
    }

    /** @return array<string, mixed> */
    public function setDefaultVersion(string $skillName, string $version): array
    {
        $response = $this->dispatchFoundry(new UpdateSkill(
            $skillName,
            new GenericJsonPayload(['default_version' => $version]),
        ));

        return $this->jsonArray($response);
    }
}
