# Microsoft Azure API reference

Auto-generated reference for Saloon requests, response DTOs, write payloads, and resource gateways.

See also: [inventory parity](inventory-parity.md) for endpoint coverage status.

## HTTP requests

| Surface | Method | Path | Request class |
| --- | --- | --- | --- |
| arm | GET | `/subscriptions` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\ListSubscriptions` |
| arm | GET | `/subscriptions/{subscriptionId}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription` |
| arm | POST | `/subscriptions/{subscriptionId}/providers/Microsoft.Subscription/cancel` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\CancelSubscription` |
| arm | PUT | `/providers/Microsoft.Subscription/aliases/{aliasName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias` |
| arm | GET | `/providers/Microsoft.Subscription/aliases/{aliasName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias` |
| arm | DELETE | `/providers/Microsoft.Subscription/aliases/{aliasName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\DeleteSubscriptionAlias` |
| arm | GET | `/providers/Microsoft.Subscription/aliases` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\ListSubscriptionAliases` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup` |
| arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup` |
| arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups` |
| arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name}/operations` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\ListDeploymentOperations` |
| arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name}/cancel` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CancelDeployment` |
| arm | PUT | `/{scope}/providers/Microsoft.Authorization/roleAssignments/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment` |
| arm | GET | `/subscriptions/{subscriptionId}/providers/Microsoft.KeyVault/deletedVaults` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults` |
| arm | POST | `/subscriptions/{subscriptionId}/providers/Microsoft.KeyVault/locations/{loc}/deletedVaults/{name}/purge` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\PurgeDeletedVault` |
| arm | GET | `/subscriptions/{subscriptionId}/providers/Microsoft.CognitiveServices/locations/{loc}/deletedAccounts` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts` |
| arm | POST | `/subscriptions/{subscriptionId}/providers/Microsoft.CognitiveServices/locations/{loc}/deletedAccounts/{name}/purge` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount` |
| arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Sql/servers/{server}/firewallRules/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule` |
| arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Sql/servers/{server}/firewallRules/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlFirewallRule` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Sql/servers/{server}/databases/{db}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase` |
| keyvault | GET | `/secrets/{name}` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret` |
| keyvault | PUT | `/secrets/{name}` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret` |
| keyvault | GET | `/secrets` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\ListSecrets` |
| keyvault | DELETE | `/secrets/{name}` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret` |
| graph | GET | `/groups` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups` |
| graph | GET | `/groups/{id}` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\GetGroup` |
| graph | POST | `/groups` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup` |
| graph | DELETE | `/groups/{id}` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup` |
| graph | GET | `/groups/{id}/members` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers` |
| graph | POST | `/groups/{id}/members/$ref` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember` |
| graph | DELETE | `/groups/{id}/members/{memberId}/$ref` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember` |
| graph | GET | `/users/{id}` | `CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser` |
| graph | GET | `/users` | `CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers` |
| graph | POST | `/invitations` | `CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation` |
| kudu | POST | `/api/zipdeploy` | `CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy` |
| kudu | GET | `/api/deployments/{id}` | `CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus` |
| auth | POST | `/oauth2/v2.0/token` | `CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest` |

## Response DTOs

| Class | Key fields |
| --- | --- |
| `CodebarAg\MicrosoftAzure\Data\Arm\CanceledSubscriptionData` | `subscriptionId` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData` | `id`, `name`, `location`, `deletionDate`, `scheduledPurgeDate` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData` | `id`, `name`, `location`, `deletionDate`, `scheduledPurgeDate` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData` | `id`, `name`, `mode`, `provisioningState`, `correlationId`, `timestamp` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeploymentOperationData` | `id`, `operationId`, `provisioningState`, `statusMessage`, `targetResource` |
| `CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData` | `id`, `name`, `location`, `provisioningState`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\RoleAssignmentData` | `id`, `name`, `scope`, `roleDefinitionId`, `principalId`, `principalType` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData` | `id`, `name`, `location`, `status`, `collation`, `edition` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData` | `id`, `name`, `startIpAddress`, `endIpAddress` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData` | `id`, `name`, `subscriptionId`, `provisioningState`, `billingScope`, `displayName`, `workload` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionData` | `id`, `subscriptionId`, `displayName`, `state`, `tenantId`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData` | `accessToken`, `tokenType`, `expiresIn`, `expiresAt` |
| `CodebarAg\MicrosoftAzure\Data\Graph\GroupData` | `id`, `displayName`, `mailNickname`, `description`, `mailEnabled`, `securityEnabled`, `groupTypes` |
| `CodebarAg\MicrosoftAzure\Data\Graph\InvitationData` | `id`, `inviteRedeemUrl`, `invitedUserEmailAddress`, `status`, `invitedUser` |
| `CodebarAg\MicrosoftAzure\Data\Graph\UserData` | `id`, `displayName`, `userPrincipalName`, `mail`, `givenName`, `surname` |
| `CodebarAg\MicrosoftAzure\Data\KeyVault\SecretData` | `id`, `name`, `value`, `contentType`, `createdOn`, `updatedOn`, `enabled` |
| `CodebarAg\MicrosoftAzure\Data\KeyVault\SecretIdentifierData` | `id`, `name`, `enabled` |
| `CodebarAg\MicrosoftAzure\Data\Kudu\KuduDeploymentData` | `id`, `status`, `author`, `deployer`, `message`, `startTime`, `endTime`, `complete`, `active` |

## Request payloads

Write operations accept typed payload DTOs (`toAzureBody()` or `toFormBody()` for OAuth).

| Payload DTO | Request | Fields |
| --- | --- | --- |
| `CodebarAg\MicrosoftAzure\Data\Payload\AddGroupMemberPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember` | `memberId` |
| `CodebarAg\MicrosoftAzure\Data\Payload\ClientCredentialsTokenPayload` | `CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest` | `clientId`, `clientSecret`, `scope` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateGroupPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup` | `displayName`, `mailNickname`, `mailEnabled`, `securityEnabled`, `groupTypes` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateInvitationPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation` | `invitedUserEmailAddress`, `inviteRedirectUrl`, `sendInvitationMessage` |
| `CodebarAg\MicrosoftAzure\Data\Payload\DeploymentPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment` | `template`, `parameters`, `mode` |
| `CodebarAg\MicrosoftAzure\Data\Payload\ResourceGroupPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup` | `location`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\RoleAssignmentPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment` | `roleDefinitionId`, `principalId`, `principalType` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret` | `value`, `attributes` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SqlFirewallRulePayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule` | `startIpAddress`, `endIpAddress` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SubscriptionAliasPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias` | `billingScope`, `displayName`, `workload`, `subscriptionId`, `additionalProperties`, `tags` |

**Note:** `ZipDeploy` sends a binary stream body and has no payload DTO.

## Resource gateways

| Resource | Method | Request | Response DTO |
| --- | --- | --- | --- |
| `AppServiceResource` | `deploymentStatus()` | `CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus` | `KuduDeploymentData` |
| `AppServiceResource` | `zipDeploy()` | `CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy` | `KuduDeploymentData` |
| `DeletedCognitiveServicesResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts` | `Collection` |
| `DeletedCognitiveServicesResource` | `purge()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount` | `—` |
| `DeletedVaultsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults` | `Collection` |
| `DeletedVaultsResource` | `purge()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\PurgeDeletedVault` | `—` |
| `DeploymentsResource` | `cancel()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CancelDeployment` | `—` |
| `DeploymentsResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment` | `DeploymentData` |
| `DeploymentsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment` | `DeploymentData` |
| `DeploymentsResource` | `operations()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\ListDeploymentOperations` | `Collection` |
| `GraphResource` | `groups()` | `GroupsResource` | `GroupsResource` |
| `GraphResource` | `invitations()` | `InvitationsResource` | `InvitationsResource` |
| `GraphResource` | `users()` | `UsersResource` | `UsersResource` |
| `GroupsResource` | `addMember()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember` | `—` |
| `GroupsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup` | `GroupData` |
| `GroupsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup` | `—` |
| `GroupsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\GetGroup` | `GroupData` |
| `GroupsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups` | `Collection` |
| `GroupsResource` | `members()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers` | `Collection` |
| `GroupsResource` | `removeMember()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember` | `—` |
| `InvitationsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation` | `InvitationData` |
| `ResourceGroupsResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup` | `ResourceGroupData` |
| `ResourceGroupsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup` | `—` |
| `ResourceGroupsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup` | `ResourceGroupData` |
| `ResourceGroupsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups` | `Collection` |
| `RoleAssignmentsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment` | `RoleAssignmentData` |
| `SecretsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret` | `SecretData` |
| `SecretsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret` | `SecretData` |
| `SecretsResource` | `set()` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret` | `SecretData` |
| `SqlDatabasesResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase` | `SqlDatabaseData` |
| `SqlFirewallRulesResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule` | `SqlFirewallRuleData` |
| `SqlFirewallRulesResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlFirewallRule` | `—` |
| `SubscriptionAliasesResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias` | `SubscriptionAliasData` |
| `SubscriptionAliasesResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\DeleteSubscriptionAlias` | `—` |
| `SubscriptionAliasesResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias` | `SubscriptionAliasData` |
| `SubscriptionsResource` | `cancel()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\CancelSubscription` | `CanceledSubscriptionData` |
| `SubscriptionsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription` | `SubscriptionData` |
| `UsersResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser` | `UserData` |
| `UsersResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers` | `Collection` |
| `VaultResource` | `secrets()` | `SecretsResource` | `SecretsResource` |

Generated at: 2026-06-30T05:51:16+00:00
