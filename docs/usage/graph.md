# Microsoft Graph

Applications, groups, invitations, service principals, and users against `https://graph.microsoft.com/v1.0`. Not ARM — a separate base URL and token audience (`https://graph.microsoft.com/.default`).

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$graph = Azure::instance()->graph();
```

## Groups

```php
$graph->groups()->list(); // optional OData $filter as the first arg
$graph->groups()->get($groupId);

$graph->groups()->create(
    displayName: 'Engineering',
    mailNickname: 'engineering',
    mailEnabled: false,
    securityEnabled: true,
);

$graph->groups()->members($groupId);
$graph->groups()->addMember($groupId, $userId);
$graph->groups()->removeMember($groupId, $userId);
$graph->groups()->delete($groupId);
```

## Users

```php
$graph->users()->list(); // optional OData $filter
$graph->users()->get($userId);
```

## Invitations (guest users)

```php
$graph->invitations()->create(
    invitedUserEmailAddress: 'partner@example.com',
    inviteRedirectUrl: 'https://myapp.example.com/welcome',
);
```

## Applications & service principals

App registration and service principal management — useful for provisioning the very kind of app registration this package itself authenticates as.

```php
$app = $graph->applications()->create(displayName: 'My Integration');
$credential = $graph->applications()->addPassword($app->id, displayName: 'default');
$graph->applications()->delete($app->id);

$graph->applications()->get($app->id);                  // by directory object id
$graph->applications()->list();                         // optional OData $filter and $select
$graph->applications()->list(
    filter: "startswith(displayName,'flows-')",
    select: 'id,appId,displayName',                     // Graph returns the whole object otherwise
);
$graph->applications()->findByAppId($app->appId);       // nullable; by client id
$graph->applications()->observe($app->id);              // actual-state read, never throws on 404/403

$graph->servicePrincipals()->create(appId: $app->appId);
$graph->servicePrincipals()->findByAppId($app->appId);       // nullable
$graph->servicePrincipals()->findByAppIdOrFail($app->appId); // throws if not found
$graph->servicePrincipals()->list(); // optional OData $filter
$graph->servicePrincipals()->delete($servicePrincipalObjectId);
```

Every Graph `list()` here returns the **first page only** — `@odata.nextLink` is not followed.
Narrow with `$filter`/`$select` rather than relying on a full enumeration; see
[Pagination](../advanced.md#pagination).

App registrations are the one provisioned family with no soft-delete state to model: a deleted
app sits in the directory recycle bin for 30 days, but `displayName` was never unique, so it
holds nothing and a re-create always succeeds. `observe()` therefore returns only `Absent`,
`Available` or `Inaccessible` here — see [Advanced usage](../advanced.md#actual-state-reads-observe).
