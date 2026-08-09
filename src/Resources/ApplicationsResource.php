<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Concerns\ObservesActualState;
use CodebarAg\MicrosoftAzure\Data\Graph\ApplicationData;
use CodebarAg\MicrosoftAzure\Data\Graph\PasswordCredentialData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Data\Payload\AddApplicationPasswordPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateApplicationPayload;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\AddApplicationPassword;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\CreateApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\DeleteApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\GetApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\ListApplications;
use Illuminate\Support\Collection;

final class ApplicationsResource extends Resource
{
    use ObservesActualState;

    public function create(string $displayName, string $signInAudience = 'AzureADMyOrg'): ApplicationData
    {
        $response = $this->sendGraph(new CreateApplication(new CreateApplicationPayload(
            $displayName,
            $signInAudience,
        )));

        return ApplicationData::fromAzure($this->jsonArray($response));
    }

    public function get(string $applicationObjectId): ApplicationData
    {
        $response = $this->sendGraph(new GetApplication($applicationObjectId));

        return ApplicationData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return Collection<int, ApplicationData>
     */
    public function list(?string $filter = null, ?string $select = null): Collection
    {
        $response = $this->sendGraph(new ListApplications($filter, $select));

        return $this->mapList($response, 'value', fn (array $item) => ApplicationData::fromAzure($item));
    }

    /**
     * Look an app registration up by its client id (`appId`) rather than its
     * directory object id, which is the identifier callers normally hold on to.
     */
    public function findByAppId(string $appId): ?ApplicationData
    {
        return $this->list("appId eq '{$appId}'")->first();
    }

    /**
     * Actual-state read: absent, available, or inaccessible.
     *
     * App registrations report no provisioning lifecycle and cannot be
     * soft-deleted in a way that holds anything — a deleted app sits in the
     * directory recycle bin for 30 days but `displayName` was never unique, so
     * it blocks nothing and a re-create always succeeds. Those three states are
     * therefore the complete set for this family, not an abbreviation of it.
     */
    public function observe(string $applicationObjectId): ResourceObservationData
    {
        return $this->observeGraph(
            new GetApplication($applicationObjectId),
            fn (array $json): ApplicationData => ApplicationData::fromAzure($json),
        );
    }

    public function addPassword(string $applicationObjectId, string $displayName = 'default'): PasswordCredentialData
    {
        $response = $this->sendGraph(new AddApplicationPassword(
            $applicationObjectId,
            new AddApplicationPasswordPayload($displayName),
        ));

        return PasswordCredentialData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $applicationObjectId): void
    {
        $this->sendGraph(new DeleteApplication($applicationObjectId));
    }
}
