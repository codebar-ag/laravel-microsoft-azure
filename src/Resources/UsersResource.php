<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Graph\UserData;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers;
use Illuminate\Support\Collection;

final class UsersResource extends Resource
{
    /**
     * @return Collection<int, UserData>
     */
    public function list(?string $filter = null): Collection
    {
        $response = $this->sendGraph(new ListUsers($filter));

        return $this->mapList($response, 'value', fn (array $item) => UserData::fromAzure($item));
    }

    public function get(string $userId): UserData
    {
        $response = $this->sendGraph(new GetUser($userId));

        return UserData::fromAzure($response->json());
    }
}
