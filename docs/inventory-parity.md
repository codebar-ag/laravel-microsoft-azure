# Microsoft Azure endpoint inventory parity

| Surface | Method | Path | Request | Tier | Status |
| --- | --- | --- | --- | --- | --- |
| arm | GET | /subscriptions | ListSubscriptions | required | Parity |
| arm | GET | /subscriptions/{subscriptionId} | GetSubscription | required | Parity |
| arm | POST | /subscriptions/{subscriptionId}/providers/Microsoft.Subscription/cancel | CancelSubscription | required | Parity |
| arm | PUT | /providers/Microsoft.Subscription/aliases/{aliasName} | CreateOrUpdateSubscriptionAlias | required | Parity |
| arm | GET | /providers/Microsoft.Subscription/aliases/{aliasName} | GetSubscriptionAlias | required | Parity |
| arm | DELETE | /providers/Microsoft.Subscription/aliases/{aliasName} | DeleteSubscriptionAlias | required | Parity |
| arm | GET | /providers/Microsoft.Subscription/aliases | ListSubscriptionAliases | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/resourceGroups/{name} | GetResourceGroup | required | Parity |
| arm | PUT | /subscriptions/{subscriptionId}/resourceGroups/{name} | CreateOrUpdateResourceGroup | required | Parity |
| arm | DELETE | /subscriptions/{subscriptionId}/resourceGroups/{name} | DeleteResourceGroup | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/resourceGroups | ListResourceGroups | required | Parity |
| arm | PUT | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name} | CreateOrUpdateDeployment | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name} | GetDeployment | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name}/operations | ListDeploymentOperations | required | Parity |
| arm | POST | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Resources/deployments/{name}/cancel | CancelDeployment | required | Parity |
| arm | PUT | /{scope}/providers/Microsoft.Authorization/roleAssignments/{name} | CreateRoleAssignment | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/providers/Microsoft.KeyVault/deletedVaults | ListDeletedVaults | required | Parity |
| arm | POST | /subscriptions/{subscriptionId}/providers/Microsoft.KeyVault/locations/{loc}/deletedVaults/{name}/purge | PurgeDeletedVault | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/providers/Microsoft.CognitiveServices/locations/{loc}/deletedAccounts | ListDeletedCognitiveServicesAccounts | required | Parity |
| arm | POST | /subscriptions/{subscriptionId}/providers/Microsoft.CognitiveServices/locations/{loc}/deletedAccounts/{name}/purge | PurgeDeletedCognitiveServicesAccount | required | Parity |
| arm | PUT | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Sql/servers/{server}/firewallRules/{name} | CreateOrUpdateSqlFirewallRule | required | Parity |
| arm | DELETE | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Sql/servers/{server}/firewallRules/{name} | DeleteSqlFirewallRule | required | Parity |
| arm | GET | /subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Sql/servers/{server}/databases/{db} | GetSqlDatabase | required | Parity |
| keyvault | GET | /secrets/{name} | GetSecret | required | Parity |
| keyvault | PUT | /secrets/{name} | SetSecret | required | Parity |
| keyvault | GET | /secrets | ListSecrets | required | Parity |
| keyvault | DELETE | /secrets/{name} | DeleteSecret | required | Parity |
| graph | GET | /groups | ListGroups | required | Parity |
| graph | GET | /groups/{id} | GetGroup | required | Parity |
| graph | POST | /groups | CreateGroup | required | Parity |
| graph | DELETE | /groups/{id} | DeleteGroup | required | Parity |
| graph | GET | /groups/{id}/members | ListGroupMembers | required | Parity |
| graph | POST | /groups/{id}/members/$ref | AddGroupMember | required | Parity |
| graph | DELETE | /groups/{id}/members/{memberId}/$ref | RemoveGroupMember | required | Parity |
| graph | GET | /users/{id} | GetUser | required | Parity |
| graph | GET | /users | ListUsers | required | Parity |
| graph | POST | /invitations | CreateInvitation | required | Parity |
| kudu | POST | /api/zipdeploy | ZipDeploy | required | Parity |
| kudu | GET | /api/deployments/{id} | GetDeploymentStatus | required | Parity |
| auth | POST | /oauth2/v2.0/token | ClientCredentialsTokenRequest | internal | Internal |

Generated at: 2026-06-30T05:28:19+00:00
