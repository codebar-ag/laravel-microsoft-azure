<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;

final class GraphResource extends Resource
{
    public function __construct(
        AzureClient $client,
    ) {
        parent::__construct($client);
    }

    public function groups(): GroupsResource
    {
        return new GroupsResource($this->client);
    }

    public function users(): UsersResource
    {
        return new UsersResource($this->client);
    }

    public function invitations(): InvitationsResource
    {
        return new InvitationsResource($this->client);
    }

    public function applications(): ApplicationsResource
    {
        return new ApplicationsResource($this->client);
    }

    public function servicePrincipals(): ServicePrincipalsResource
    {
        return new ServicePrincipalsResource($this->client);
    }
}
