<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Graph\ApplicationData;
use CodebarAg\MicrosoftAzure\Data\Graph\PasswordCredentialData;
use CodebarAg\MicrosoftAzure\Data\Payload\AddApplicationPasswordPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateApplicationPayload;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\AddApplicationPassword;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\CreateApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\DeleteApplication;

final class ApplicationsResource extends Resource
{
    public function create(string $displayName, string $signInAudience = 'AzureADMyOrg'): ApplicationData
    {
        $response = $this->sendGraph(new CreateApplication(new CreateApplicationPayload(
            $displayName,
            $signInAudience,
        )));

        return ApplicationData::fromAzure($this->jsonArray($response));
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
