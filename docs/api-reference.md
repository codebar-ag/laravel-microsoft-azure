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
| arm | GET | `/subscriptions/{subscriptionId}/providers/Microsoft.CognitiveServices/accounts` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccounts` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountsByResourceGroup` |
| arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\CreateOrUpdateCognitiveServicesAccount` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\GetCognitiveServicesAccount` |
| arm | PATCH | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\UpdateCognitiveServicesAccount` |
| arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\DeleteCognitiveServicesAccount` |
| arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/listKeys` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountKeys` |
| arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/regenerateKey` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\RegenerateCognitiveServicesAccountKey` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/models` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountModels` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/skus` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountSkus` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/projects` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\ListFoundryProjects` |
| arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/projects/{project}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/projects/{project}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject` |
| arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/projects/{project}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\DeleteFoundryProject` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/deployments` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeployments` |
| arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/deployments/{deployment}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\CreateOrUpdateModelDeployment` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/deployments/{deployment}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\GetModelDeployment` |
| arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/deployments/{deployment}` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\DeleteModelDeployment` |
| arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts/{account}/deployments/{deployment}/skus` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeploymentSkus` |
| logic | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\CreateOrUpdateLogicWorkflow` |
| logic | PATCH | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\UpdateLogicWorkflow` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GetLogicWorkflow` |
| logic | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DeleteLogicWorkflow` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsByResourceGroup` |
| logic | GET | `/subscriptions/{subscriptionId}/providers/Microsoft.Logic/workflows` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsBySubscription` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/enable` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\EnableLogicWorkflow` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/disable` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DisableLogicWorkflow` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/listCallbackUrl` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowCallbackUrl` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/generateUpgradedDefinition` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GenerateUpgradedDefinition` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/regenerateAccessKey` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\RegenerateLogicWorkflowAccessKey` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/validate` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ValidateLogicWorkflow` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/versions` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\ListLogicWorkflowVersions` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/versions/{versionId}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\GetLogicWorkflowVersion` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggers` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTrigger` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/run` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\RunLogicWorkflowTrigger` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/reset` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ResetLogicWorkflowTrigger` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/listCallbackUrl` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggerCallbackUrl` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/schemas/json` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTriggerSchemaJson` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/setState` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\SetLogicWorkflowTriggerState` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/histories` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ListLogicWorkflowTriggerHistories` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/histories/{history}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\GetLogicWorkflowTriggerHistory` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/triggers/{trigger}/histories/{history}/resubmit` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ResubmitLogicWorkflowTriggerHistory` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/runs` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\ListLogicWorkflowRuns` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/runs/{run}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\GetLogicWorkflowRun` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/runs/{run}/cancel` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\CancelLogicWorkflowRun` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/runs/{run}/actions` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActions` |
| logic | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/runs/{run}/actions/{action}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\GetLogicWorkflowRunAction` |
| logic | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Logic/workflows/{name}/runs/{run}/actions/{action}/listExpressionTraces` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActionExpressionTraces` |
| functions_arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\ListSitesByResourceGroup` |
| functions_arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSite` |
| functions_arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSite` |
| functions_arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\DeleteSite` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/restart` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\RestartSite` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/start` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StartSite` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/stop` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StopSite` |
| functions_arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/config/web` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSiteConfig` |
| functions_arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/config/web` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSiteConfig` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/config/appsettings/list` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListApplicationSettings` |
| functions_arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/config/appsettings` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\UpdateApplicationSettings` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/config/connectionstrings/list` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListConnectionStrings` |
| functions_arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/functions` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\ListFunctions` |
| functions_arm | GET | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/functions/{functionName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\GetFunction` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/host/default/listkeys` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListHostKeys` |
| functions_arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/host/default/keys/{keyName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateHostKey` |
| functions_arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/host/default/keys/{keyName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteHostKey` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/functions/{functionName}/listkeys` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListFunctionKeys` |
| functions_arm | PUT | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/functions/{functionName}/keys/{keyName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateFunctionKey` |
| functions_arm | DELETE | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/functions/{functionName}/keys/{keyName}` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteFunctionKey` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/syncfunctiontriggers` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\SyncFunctionTriggers` |
| functions_arm | POST | `/subscriptions/{subscriptionId}/resourceGroups/{rg}/providers/Microsoft.Web/sites/{name}/syncfunctiontriggers/status` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\ListSyncFunctionTriggersStatus` |
| openai | POST | `/openai/deployments/{deployment}/chat/completions` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\ChatCompletions` |
| openai | POST | `/openai/deployments/{deployment}/embeddings` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\Embeddings` |
| openai | GET | `/openai/models` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\ListModels` |
| openai | POST | `/openai/responses` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateResponses` |
| openai | POST | `/openai/deployments/{deployment}/audio/speech` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateSpeech` |
| openai | POST | `/openai/deployments/{deployment}/audio/transcriptions` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateTranscription` |
| openai | POST | `/openai/deployments/{deployment}/images/generations` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateImageGeneration` |
| openai | GET | `/openai/files` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\ListFiles` |
| openai | POST | `/openai/files` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\UploadFile` |
| openai | DELETE | `/openai/files/{fileId}` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\DeleteFile` |
| openai | POST | `/openai/fine_tuning/jobs` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateFineTuningJob` |
| openai_v1 | POST | `/openai/v1/chat/completions` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ChatCompletions` |
| openai_v1 | POST | `/openai/v1/embeddings` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1Embeddings` |
| openai_v1 | POST | `/openai/v1/responses` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateResponse` |
| openai_v1 | GET | `/openai/v1/models` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ListModels` |
| openai_v1 | GET | `/openai/v1/files` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ListFiles` |
| openai_v1 | POST | `/openai/v1/files` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1UploadFile` |
| openai_v1 | DELETE | `/openai/v1/files/{fileId}` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1DeleteFile` |
| openai_v1 | POST | `/openai/v1/images/generations` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateImageGeneration` |
| openai_v1 | POST | `/openai/v1/audio/speech` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateSpeech` |
| openai_v1 | POST | `/openai/v1/audio/transcriptions` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateTranscription` |
| openai_v1 | POST | `/openai/v1/fine_tuning/jobs` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateFineTuningJob` |
| foundry | GET | `/agents` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents` |
| foundry | POST | `/agents` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent` |
| foundry | GET | `/agents/{name}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent` |
| foundry | POST | `/agents/{name}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgent` |
| foundry | DELETE | `/agents/{name}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent` |
| foundry | POST | `/agents/{name}/versions` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion` |
| foundry | GET | `/agents/{name}/versions` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentVersions` |
| foundry | GET | `/agents/{name}/versions/{version}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion` |
| foundry | DELETE | `/agents/{name}/versions/{version}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgentVersion` |
| foundry | POST | `/agents/{name}/endpoint/protocols/openai/responses` | `CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointResponse` |
| foundry | POST | `/agents/{name}/endpoint/protocols/invocations` | `CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointInvocation` |
| foundry | POST | `/conversations` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation` |
| foundry | GET | `/conversations/{id}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation` |
| foundry | POST | `/conversations/{id}/items` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems` |
| foundry | POST | `/responses` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse` |
| foundry | POST | `/threads` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThread` |
| foundry | GET | `/threads/{id}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThread` |
| foundry | POST | `/threads/{id}/messages` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadMessage` |
| foundry | GET | `/threads/{id}/messages` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\ListThreadMessages` |
| foundry | POST | `/threads/{id}/runs` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadRun` |
| foundry | GET | `/threads/{id}/runs/{runId}` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThreadRun` |
| foundry | POST | `/threads/{id}/runs/{runId}/submit_tool_outputs` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\SubmitThreadToolOutputs` |
| function_runtime | POST | `/api/agents/{agentName}/run` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunDurableAgent` |
| function_runtime | POST | `/api/workflows/{workflowName}/run` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunWorkflow` |
| function_runtime | GET | `/api/workflows/{workflowName}/status/{runId}` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\GetWorkflowStatus` |
| function_runtime | POST | `/api/workflows/{workflowName}/respond/{runId}` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RespondToWorkflow` |
| log_analytics_query | POST | `/workspaces/{workspaceId}/query` | `CodebarAg\MicrosoftAzure\Requests\LogAnalytics\ExecuteWorkspaceQuery` |
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
| graph | POST | `/applications` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\CreateApplication` |
| graph | POST | `/applications/{id}/addPassword` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\AddApplicationPassword` |
| graph | DELETE | `/applications/{id}` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\DeleteApplication` |
| graph | GET | `/servicePrincipals` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\ListServicePrincipals` |
| graph | POST | `/servicePrincipals` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\CreateServicePrincipal` |
| graph | DELETE | `/servicePrincipals/{id}` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\DeleteServicePrincipal` |
| kudu | POST | `/api/zipdeploy` | `CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy` |
| kudu | GET | `/api/deployments/{id}` | `CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus` |
| auth | POST | `/oauth2/v2.0/token` | `CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest` |

## Response DTOs

| Class | Key fields |
| --- | --- |
| `CodebarAg\MicrosoftAzure\Data\Arm\ApiKeysData` | `key1`, `key2` |
| `CodebarAg\MicrosoftAzure\Data\Arm\ApplicationInsightsComponentData` | `id`, `name`, `location`, `instrumentationKey`, `connectionString`, `appId`, `provisioningState` |
| `CodebarAg\MicrosoftAzure\Data\Arm\BlobContainerData` | `id`, `name`, `publicAccess` |
| `CodebarAg\MicrosoftAzure\Data\Arm\CanceledSubscriptionData` | `subscriptionId` |
| `CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesAccountData` | `id`, `name`, `location`, `kind`, `skuName`, `endpoint`, `provisioningState`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesModelData` | `name`, `version`, `format` |
| `CodebarAg\MicrosoftAzure\Data\Arm\CostQueryResultData` | `columns`, `rows`, `currency` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData` | `id`, `name`, `location`, `deletionDate`, `scheduledPurgeDate` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData` | `id`, `name`, `location`, `deletionDate`, `scheduledPurgeDate` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData` | `id`, `name`, `mode`, `provisioningState`, `correlationId`, `timestamp`, `outputs`, `error` |
| `CodebarAg\MicrosoftAzure\Data\Arm\DeploymentOperationData` | `id`, `operationId`, `provisioningState`, `statusMessage`, `targetResource` |
| `CodebarAg\MicrosoftAzure\Data\Arm\FoundryProjectData` | `id`, `name`, `location`, `provisioningState` |
| `CodebarAg\MicrosoftAzure\Data\Arm\FunctionData` | `id`, `name`, `language`, `isDisabled`, `scriptHref`, `testData` |
| `CodebarAg\MicrosoftAzure\Data\Arm\HostKeysData` | `properties` |
| `CodebarAg\MicrosoftAzure\Data\Arm\KeyVaultData` | `id`, `name`, `location`, `vaultUri`, `provisioningState`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogAnalyticsWorkspaceData` | `id`, `name`, `location`, `customerId`, `provisioningState`, `skuName`, `retentionInDays` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicCallbackUrlData` | `value`, `method`, `basePath`, `relativePath`, `queries` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowData` | `id`, `name`, `location`, `state`, `provisioningState`, `accessEndpoint`, `createdTime`, `changedTime`, `version`, `definition`, `parameters` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunActionData` | `id`, `name`, `status`, `code`, `startTime`, `endTime`, `trackingId` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunData` | `id`, `name`, `status`, `code`, `startTime`, `endTime`, `triggerName`, `clientTrackingId` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerData` | `id`, `name`, `state`, `provisioningState`, `status`, `lastExecutionTime`, `nextExecutionTime` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerHistoryData` | `id`, `name`, `status`, `code`, `startTime`, `endTime`, `fired`, `runName` |
| `CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowVersionData` | `id`, `name`, `state`, `createdTime`, `changedTime`, `definition` |
| `CodebarAg\MicrosoftAzure\Data\Arm\MetricResultData` | `name`, `unit`, `points` |
| `CodebarAg\MicrosoftAzure\Data\Arm\ModelDeploymentData` | `id`, `name`, `modelFormat`, `modelName`, `modelVersion`, `skuName`, `skuCapacity`, `provisioningState` |
| `CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData` | `id`, `name`, `location`, `provisioningState`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\ResourceProviderData` | `namespace`, `registrationState`, `id` |
| `CodebarAg\MicrosoftAzure\Data\Arm\RoleAssignmentData` | `id`, `name`, `scope`, `roleDefinitionId`, `principalId`, `principalType` |
| `CodebarAg\MicrosoftAzure\Data\Arm\RoleDefinitionData` | `id`, `name`, `roleName`, `type` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData` | `id`, `name`, `location`, `status`, `collation`, `edition`, `currentServiceObjectiveName`, `autoPauseDelay` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData` | `id`, `name`, `startIpAddress`, `endIpAddress` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SqlServerData` | `id`, `name`, `location`, `fullyQualifiedDomainName`, `state`, `provisioningState` |
| `CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountData` | `id`, `name`, `location`, `skuName`, `kind`, `provisioningState`, `primaryBlobEndpoint`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountKeysData` | `keys` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData` | `id`, `name`, `subscriptionId`, `provisioningState`, `billingScope`, `displayName`, `workload` |
| `CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionData` | `id`, `subscriptionId`, `displayName`, `state`, `tenantId`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\UsageDetailData` | `id`, `name`, `cost`, `currency`, `date`, `product`, `meterName` |
| `CodebarAg\MicrosoftAzure\Data\Arm\UserAssignedIdentityData` | `id`, `name`, `location`, `principalId`, `clientId`, `tenantId`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Arm\WebSiteData` | `id`, `name`, `location`, `kind`, `defaultHostName`, `state`, `provisioningState`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData` | `accessToken`, `tokenType`, `expiresIn`, `expiresAt` |
| `CodebarAg\MicrosoftAzure\Data\Graph\ApplicationData` | `id`, `appId`, `displayName` |
| `CodebarAg\MicrosoftAzure\Data\Graph\GroupData` | `id`, `displayName`, `mailNickname`, `description`, `mailEnabled`, `securityEnabled`, `groupTypes` |
| `CodebarAg\MicrosoftAzure\Data\Graph\InvitationData` | `id`, `inviteRedeemUrl`, `invitedUserEmailAddress`, `status`, `invitedUser` |
| `CodebarAg\MicrosoftAzure\Data\Graph\PasswordCredentialData` | `secretText`, `keyId`, `displayName` |
| `CodebarAg\MicrosoftAzure\Data\Graph\ServicePrincipalData` | `id`, `appId`, `displayName` |
| `CodebarAg\MicrosoftAzure\Data\Graph\UserData` | `id`, `displayName`, `userPrincipalName`, `mail`, `givenName`, `surname` |
| `CodebarAg\MicrosoftAzure\Data\KeyVault\SecretData` | `id`, `name`, `value`, `contentType`, `createdOn`, `updatedOn`, `enabled` |
| `CodebarAg\MicrosoftAzure\Data\KeyVault\SecretIdentifierData` | `id`, `name`, `enabled` |
| `CodebarAg\MicrosoftAzure\Data\Kudu\KuduDeploymentData` | `id`, `status`, `author`, `deployer`, `message`, `startTime`, `endTime`, `complete`, `active` |
| `CodebarAg\MicrosoftAzure\Data\LogAnalytics\QueryResultsData` | `tables` |
| `CodebarAg\MicrosoftAzure\Data\LogAnalytics\QueryTableData` | `name`, `columns`, `rows` |
| `CodebarAg\MicrosoftAzure\Data\OpenAi\ChatCompletionUsageData` | `promptTokens`, `completionTokens`, `totalTokens`, `id`, `model`, `choices`, `usage` |
| `CodebarAg\MicrosoftAzure\Data\OpenAi\EmbeddingData` | `model`, `data`, `promptTokens`, `totalTokens` |
| `CodebarAg\MicrosoftAzure\Data\OpenAi\ModelListData` | `data` |
| `CodebarAg\MicrosoftAzure\Data\OpenAi\OpenAiResponseData` | `id`, `model`, `status`, `output`, `usage` |

## Request payloads

Write operations accept typed payload DTOs (`toAzureBody()` or `toFormBody()` for OAuth).

| Payload DTO | Request | Fields |
| --- | --- | --- |
| `CodebarAg\MicrosoftAzure\Data\Payload\AddApplicationPasswordPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\AddApplicationPassword` | `displayName` |
| `CodebarAg\MicrosoftAzure\Data\Payload\AddGroupMemberPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember` | `memberId` |
| `CodebarAg\MicrosoftAzure\Data\Payload\AppSettingsPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\UpdateApplicationSettings` | `properties` |
| `CodebarAg\MicrosoftAzure\Data\Payload\ApplicationInsightsComponentPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\CreateOrUpdateComponent` | `location`, `applicationType`, `kind`, `workspaceResourceId`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\BlobContainerPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateBlobContainer` | `publicAccess`, `properties` |
| `CodebarAg\MicrosoftAzure\Data\Payload\BlobManagementPolicyPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\SetBlobManagementPolicy` | `rules` |
| `CodebarAg\MicrosoftAzure\Data\Payload\ClientCredentialsTokenPayload` | `CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest` | `clientId`, `clientSecret`, `scope` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CognitiveServicesAccountPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\CreateOrUpdateCognitiveServicesAccount` | `location`, `kind`, `skuName`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost` | `from`, `to`, `grouping` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload` | `—` | `name`, `definition`, `description`, `metadata` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateApplicationPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\CreateApplication` | `displayName`, `signInAudience` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateGroupPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup` | `displayName`, `mailNickname`, `mailEnabled`, `securityEnabled`, `groupTypes` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateInvitationPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation` | `invitedUserEmailAddress`, `inviteRedirectUrl`, `sendInvitationMessage` |
| `CodebarAg\MicrosoftAzure\Data\Payload\CreateServicePrincipalPayload` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\CreateServicePrincipal` | `appId` |
| `CodebarAg\MicrosoftAzure\Data\Payload\DeploymentPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment` | `template`, `parameters`, `mode` |
| `CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject` | `location`, `properties` |
| `CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateFunctionKey` | `value` |
| `CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\ChatCompletions` | `body` |
| `CodebarAg\MicrosoftAzure\Data\Payload\HostedAgentDefinitionPayload` | `—` | `containerProtocolVersions`, `cpu`, `memory`, `image`, `environmentVariables`, `tools`, `raiConfig` |
| `CodebarAg\MicrosoftAzure\Data\Payload\KeyVaultPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\CreateOrUpdateVault` | `location`, `tenantId`, `skuName`, `enableRbacAuthorization`, `enablePurgeProtection`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsQueryPayload` | `CodebarAg\MicrosoftAzure\Requests\LogAnalytics\ExecuteWorkspaceQuery` | `query`, `timespan` |
| `CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsWorkspacePayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\CreateOrUpdateWorkspace` | `location`, `skuName`, `retentionInDays`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\LogicWorkflowPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\CreateOrUpdateLogicWorkflow` | `location`, `definition`, `parameters`, `state`, `integrationAccountId`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\ModelDeploymentPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\CreateOrUpdateModelDeployment` | `modelFormat`, `modelName`, `modelVersion`, `skuName`, `skuCapacity` |
| `CodebarAg\MicrosoftAzure\Data\Payload\RaiConfigPayload` | `—` | `raiPolicyName` |
| `CodebarAg\MicrosoftAzure\Data\Payload\RegenerateKeyPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\RegenerateCognitiveServicesAccountKey` | `keyName` |
| `CodebarAg\MicrosoftAzure\Data\Payload\RegenerateStorageKeyPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\RegenerateStorageAccountKey` | `keyName` |
| `CodebarAg\MicrosoftAzure\Data\Payload\ResourceGroupPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup` | `location`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\RoleAssignmentPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment` | `roleDefinitionId`, `principalId`, `principalType` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret` | `value`, `attributes` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SqlDatabasePayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlDatabase` | `location`, `skuName`, `tier`, `family`, `capacity`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SqlFirewallRulePayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule` | `startIpAddress`, `endIpAddress` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SqlServerPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlServer` | `location`, `administratorLogin`, `version`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\StorageAccountPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageAccount` | `location`, `skuName`, `kind`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\SubscriptionAliasPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias` | `billingScope`, `displayName`, `workload`, `subscriptionId`, `additionalProperties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\UpdateAgentPayload` | `—` | `definition`, `description`, `metadata` |
| `CodebarAg\MicrosoftAzure\Data\Payload\UserAssignedIdentityPayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\CreateOrUpdateUserAssignedIdentity` | `location`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\WebSitePayload` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSite` | `location`, `kind`, `properties`, `tags` |
| `CodebarAg\MicrosoftAzure\Data\Payload\WorkflowAgentDefinitionPayload` | `—` | `workflow`, `raiConfig` |

**Note:** `ZipDeploy` sends a binary stream body and has no payload DTO.

## Resource gateways

| Resource | Method | Request | Response DTO |
| --- | --- | --- | --- |
| `AppServiceResource` | `deploymentStatus()` | `CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus` | `KuduDeploymentData` |
| `AppServiceResource` | `zipDeploy()` | `CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy` | `KuduDeploymentData` |
| `ApplicationInsightsComponentResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\CreateOrUpdateComponent` | `ApplicationInsightsComponentData` |
| `ApplicationInsightsComponentResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\DeleteComponent` | `—` |
| `ApplicationInsightsComponentResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\GetComponent` | `ApplicationInsightsComponentData` |
| `ApplicationInsightsResource` | `component()` | `ApplicationInsightsComponentResource` | `ApplicationInsightsComponentResource` |
| `ApplicationInsightsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\ListComponentsByResourceGroup` | `Collection` |
| `ApplicationsResource` | `addPassword()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\AddApplicationPassword` | `PasswordCredentialData` |
| `ApplicationsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\CreateApplication` | `ApplicationData` |
| `ApplicationsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Applications\DeleteApplication` | `—` |
| `BlobContainersResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateBlobContainer` | `BlobContainerData` |
| `BlobContainersResource` | `setManagementPolicy()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\SetBlobManagementPolicy` | `—` |
| `CognitiveServicesAccountResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\CreateOrUpdateCognitiveServicesAccount` | `CognitiveServicesAccountData` |
| `CognitiveServicesAccountResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\DeleteCognitiveServicesAccount` | `—` |
| `CognitiveServicesAccountResource` | `deployments()` | `ModelDeploymentsResource` | `ModelDeploymentsResource` |
| `CognitiveServicesAccountResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\GetCognitiveServicesAccount` | `CognitiveServicesAccountData` |
| `CognitiveServicesAccountResource` | `listKeys()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountKeys` | `ApiKeysData` |
| `CognitiveServicesAccountResource` | `listModels()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountModels` | `Collection` |
| `CognitiveServicesAccountResource` | `listSkus()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountSkus` | `Collection` |
| `CognitiveServicesAccountResource` | `projects()` | `FoundryProjectsResource` | `FoundryProjectsResource` |
| `CognitiveServicesAccountResource` | `regenerateKey()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\RegenerateCognitiveServicesAccountKey` | `ApiKeysData` |
| `CognitiveServicesAccountResource` | `update()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\UpdateCognitiveServicesAccount` | `CognitiveServicesAccountData` |
| `CognitiveServicesResource` | `account()` | `CognitiveServicesAccountResource` | `CognitiveServicesAccountResource` |
| `CognitiveServicesResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountsByResourceGroup` | `Collection` |
| `CognitiveServicesResource` | `listAllInSubscription()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccounts` | `Collection` |
| `ConsumptionResource` | `usageDetails()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Consumption\ListUsageDetails` | `Collection` |
| `CostManagementResource` | `query()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost` | `CostQueryResultData` |
| `DeletedCognitiveServicesResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts` | `Collection` |
| `DeletedCognitiveServicesResource` | `purge()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount` | `—` |
| `DeletedVaultsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults` | `Collection` |
| `DeletedVaultsResource` | `purge()` | `CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\PurgeDeletedVault` | `—` |
| `DeploymentsResource` | `cancel()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CancelDeployment` | `—` |
| `DeploymentsResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment` | `DeploymentData` |
| `DeploymentsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment` | `DeploymentData` |
| `DeploymentsResource` | `operations()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\ListDeploymentOperations` | `Collection` |
| `DurableAgentRuntimeResource` | `run()` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunDurableAgent` | `array` |
| `FoundryAgentEndpointResource` | `createInvocation()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointInvocation` | `array` |
| `FoundryAgentEndpointResource` | `createResponse()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointResponse` | `array` |
| `FoundryAgentResource` | `endpoint()` | `FoundryAgentEndpointResource` | `FoundryAgentEndpointResource` |
| `FoundryAgentsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent` | `array` |
| `FoundryAgentsResource` | `createVersion()` | `GenericJsonPayload` | `array` |
| `FoundryAgentsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent` | `—` |
| `FoundryAgentsResource` | `deleteVersion()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgentVersion` | `—` |
| `FoundryAgentsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent` | `array` |
| `FoundryAgentsResource` | `getVersion()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion` | `array` |
| `FoundryAgentsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents` | `Collection` |
| `FoundryAgentsResource` | `listVersions()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentVersions` | `Collection` |
| `FoundryAgentsResource` | `update()` | `GenericJsonPayload` | `array` |
| `FoundryConversationsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation` | `array` |
| `FoundryConversationsResource` | `createItems()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems` | `array` |
| `FoundryConversationsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation` | `array` |
| `FoundryProjectsResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject` | `FoundryProjectData` |
| `FoundryProjectsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\DeleteFoundryProject` | `—` |
| `FoundryProjectsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject` | `FoundryProjectData` |
| `FoundryProjectsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\ListFoundryProjects` | `Collection` |
| `FoundryResource` | `agent()` | `FoundryAgentResource` | `FoundryAgentResource` |
| `FoundryResource` | `agents()` | `FoundryAgentsResource` | `FoundryAgentsResource` |
| `FoundryResource` | `conversations()` | `FoundryConversationsResource` | `FoundryConversationsResource` |
| `FoundryResource` | `responses()` | `FoundryResponsesResource` | `FoundryResponsesResource` |
| `FoundryResource` | `threads()` | `FoundryThreadsResource` | `FoundryThreadsResource` |
| `FoundryResponsesResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse` | `array` |
| `FoundryThreadsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThread` | `array` |
| `FoundryThreadsResource` | `createMessage()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadMessage` | `array` |
| `FoundryThreadsResource` | `createRun()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadRun` | `array` |
| `FoundryThreadsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThread` | `array` |
| `FoundryThreadsResource` | `getRun()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThreadRun` | `array` |
| `FoundryThreadsResource` | `listMessages()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\ListThreadMessages` | `Collection` |
| `FoundryThreadsResource` | `submitToolOutputs()` | `CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\SubmitThreadToolOutputs` | `array` |
| `FunctionAppHostKeysResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateHostKey` | `HostKeysData` |
| `FunctionAppHostKeysResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteHostKey` | `—` |
| `FunctionAppHostKeysResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListHostKeys` | `HostKeysData` |
| `FunctionAppResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSite` | `WebSiteData` |
| `FunctionAppResource` | `createOrUpdateConfig()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSiteConfig` | `array` |
| `FunctionAppResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\DeleteSite` | `—` |
| `FunctionAppResource` | `functions()` | `FunctionResource` | `FunctionResource` |
| `FunctionAppResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSite` | `WebSiteData` |
| `FunctionAppResource` | `getConfig()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSiteConfig` | `array` |
| `FunctionAppResource` | `hostKeys()` | `FunctionAppHostKeysResource` | `FunctionAppHostKeysResource` |
| `FunctionAppResource` | `listFunctions()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\ListFunctions` | `Collection` |
| `FunctionAppResource` | `restart()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\RestartSite` | `—` |
| `FunctionAppResource` | `settings()` | `FunctionAppSettingsResource` | `FunctionAppSettingsResource` |
| `FunctionAppResource` | `start()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StartSite` | `—` |
| `FunctionAppResource` | `stop()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StopSite` | `—` |
| `FunctionAppResource` | `syncTriggers()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\SyncFunctionTriggers` | `—` |
| `FunctionAppResource` | `syncTriggersStatus()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\ListSyncFunctionTriggersStatus` | `array` |
| `FunctionAppSettingsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListApplicationSettings` | `array` |
| `FunctionAppSettingsResource` | `listConnectionStrings()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListConnectionStrings` | `array` |
| `FunctionAppSettingsResource` | `update()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\UpdateApplicationSettings` | `array` |
| `FunctionAppsResource` | `app()` | `FunctionAppResource` | `FunctionAppResource` |
| `FunctionAppsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\ListSitesByResourceGroup` | `Collection` |
| `FunctionKeysResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateFunctionKey` | `HostKeysData` |
| `FunctionKeysResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteFunctionKey` | `—` |
| `FunctionKeysResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListFunctionKeys` | `HostKeysData` |
| `FunctionResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\GetFunction` | `FunctionData` |
| `FunctionResource` | `keys()` | `FunctionKeysResource` | `FunctionKeysResource` |
| `FunctionRuntimeResource` | `agents()` | `DurableAgentRuntimeResource` | `DurableAgentRuntimeResource` |
| `FunctionRuntimeResource` | `workflows()` | `WorkflowRuntimeResource` | `WorkflowRuntimeResource` |
| `GraphResource` | `applications()` | `ApplicationsResource` | `ApplicationsResource` |
| `GraphResource` | `groups()` | `GroupsResource` | `GroupsResource` |
| `GraphResource` | `invitations()` | `InvitationsResource` | `InvitationsResource` |
| `GraphResource` | `servicePrincipals()` | `ServicePrincipalsResource` | `ServicePrincipalsResource` |
| `GraphResource` | `users()` | `UsersResource` | `UsersResource` |
| `GroupsResource` | `addMember()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember` | `—` |
| `GroupsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup` | `GroupData` |
| `GroupsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup` | `—` |
| `GroupsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\GetGroup` | `GroupData` |
| `GroupsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups` | `Collection` |
| `GroupsResource` | `members()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers` | `Collection` |
| `GroupsResource` | `removeMember()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember` | `—` |
| `InvitationsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation` | `InvitationData` |
| `KeyVaultVaultResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\CreateOrUpdateVault` | `KeyVaultData` |
| `KeyVaultVaultResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\DeleteVault` | `—` |
| `KeyVaultVaultResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\GetVault` | `KeyVaultData` |
| `KeyVaultsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\ListVaultsByResourceGroup` | `Collection` |
| `KeyVaultsResource` | `vault()` | `KeyVaultVaultResource` | `KeyVaultVaultResource` |
| `LogAnalyticsQueryResource` | `query()` | `CodebarAg\MicrosoftAzure\Requests\LogAnalytics\ExecuteWorkspaceQuery` | `QueryResultsData` |
| `LogAnalyticsWorkspaceResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\CreateOrUpdateWorkspace` | `LogAnalyticsWorkspaceData` |
| `LogAnalyticsWorkspaceResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\DeleteWorkspace` | `—` |
| `LogAnalyticsWorkspaceResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\GetWorkspace` | `LogAnalyticsWorkspaceData` |
| `LogAnalyticsWorkspacesResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\ListWorkspacesByResourceGroup` | `Collection` |
| `LogAnalyticsWorkspacesResource` | `workspace()` | `LogAnalyticsWorkspaceResource` | `LogAnalyticsWorkspaceResource` |
| `LogicWorkflowResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\CreateOrUpdateLogicWorkflow` | `LogicWorkflowData` |
| `LogicWorkflowResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DeleteLogicWorkflow` | `—` |
| `LogicWorkflowResource` | `disable()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DisableLogicWorkflow` | `—` |
| `LogicWorkflowResource` | `enable()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\EnableLogicWorkflow` | `—` |
| `LogicWorkflowResource` | `generateUpgradedDefinition()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GenerateUpgradedDefinition` | `array` |
| `LogicWorkflowResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GetLogicWorkflow` | `LogicWorkflowData` |
| `LogicWorkflowResource` | `listCallbackUrl()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowCallbackUrl` | `LogicCallbackUrlData` |
| `LogicWorkflowResource` | `regenerateAccessKey()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\RegenerateLogicWorkflowAccessKey` | `—` |
| `LogicWorkflowResource` | `runs()` | `LogicWorkflowRunsResource` | `LogicWorkflowRunsResource` |
| `LogicWorkflowResource` | `triggers()` | `LogicWorkflowTriggersResource` | `LogicWorkflowTriggersResource` |
| `LogicWorkflowResource` | `update()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\UpdateLogicWorkflow` | `LogicWorkflowData` |
| `LogicWorkflowResource` | `validate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ValidateLogicWorkflow` | `—` |
| `LogicWorkflowResource` | `versions()` | `LogicWorkflowVersionsResource` | `LogicWorkflowVersionsResource` |
| `LogicWorkflowRunActionsResource` | `expressionTraces()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActionExpressionTraces` | `Collection` |
| `LogicWorkflowRunActionsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\GetLogicWorkflowRunAction` | `LogicWorkflowRunActionData` |
| `LogicWorkflowRunActionsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActions` | `Collection` |
| `LogicWorkflowRunResource` | `actions()` | `LogicWorkflowRunActionsResource` | `LogicWorkflowRunActionsResource` |
| `LogicWorkflowRunResource` | `cancel()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\CancelLogicWorkflowRun` | `—` |
| `LogicWorkflowRunResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\GetLogicWorkflowRun` | `LogicWorkflowRunData` |
| `LogicWorkflowRunsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\ListLogicWorkflowRuns` | `Collection` |
| `LogicWorkflowRunsResource` | `run()` | `LogicWorkflowRunResource` | `LogicWorkflowRunResource` |
| `LogicWorkflowTriggerHistoriesResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\GetLogicWorkflowTriggerHistory` | `LogicWorkflowTriggerHistoryData` |
| `LogicWorkflowTriggerHistoriesResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ListLogicWorkflowTriggerHistories` | `Collection` |
| `LogicWorkflowTriggerHistoriesResource` | `resubmit()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ResubmitLogicWorkflowTriggerHistory` | `—` |
| `LogicWorkflowTriggerResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTrigger` | `LogicWorkflowTriggerData` |
| `LogicWorkflowTriggerResource` | `histories()` | `LogicWorkflowTriggerHistoriesResource` | `LogicWorkflowTriggerHistoriesResource` |
| `LogicWorkflowTriggerResource` | `listCallbackUrl()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggerCallbackUrl` | `LogicCallbackUrlData` |
| `LogicWorkflowTriggerResource` | `reset()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ResetLogicWorkflowTrigger` | `—` |
| `LogicWorkflowTriggerResource` | `run()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\RunLogicWorkflowTrigger` | `—` |
| `LogicWorkflowTriggerResource` | `schemaJson()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTriggerSchemaJson` | `array` |
| `LogicWorkflowTriggerResource` | `setState()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\SetLogicWorkflowTriggerState` | `—` |
| `LogicWorkflowTriggersResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggers` | `Collection` |
| `LogicWorkflowTriggersResource` | `trigger()` | `LogicWorkflowTriggerResource` | `LogicWorkflowTriggerResource` |
| `LogicWorkflowVersionsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\GetLogicWorkflowVersion` | `LogicWorkflowVersionData` |
| `LogicWorkflowVersionsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\ListLogicWorkflowVersions` | `Collection` |
| `LogicWorkflowsResource` | `createOrUpdate()` | `LogicWorkflowPayload` | `LogicWorkflowData` |
| `LogicWorkflowsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsByResourceGroup` | `Collection` |
| `LogicWorkflowsResource` | `listBySubscription()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsBySubscription` | `Collection` |
| `LogicWorkflowsResource` | `workflow()` | `LogicWorkflowResource` | `LogicWorkflowResource` |
| `ManagedIdentitiesResource` | `identity()` | `UserAssignedIdentityResource` | `UserAssignedIdentityResource` |
| `ManagedIdentitiesResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\ListUserAssignedIdentitiesByResourceGroup` | `Collection` |
| `MetricsResource` | `definitions()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\ListMetricDefinitions` | `Collection` |
| `MetricsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\GetMetrics` | `Collection` |
| `ModelDeploymentsResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\CreateOrUpdateModelDeployment` | `ModelDeploymentData` |
| `ModelDeploymentsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\DeleteModelDeployment` | `—` |
| `ModelDeploymentsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\GetModelDeployment` | `ModelDeploymentData` |
| `ModelDeploymentsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeployments` | `Collection` |
| `ModelDeploymentsResource` | `listSkus()` | `CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeploymentSkus` | `Collection` |
| `OpenAiAudioResource` | `speech()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateSpeech` | `array` |
| `OpenAiAudioResource` | `transcription()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateTranscription` | `array` |
| `OpenAiChatResource` | `completions()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\ChatCompletions` | `ChatCompletionData` |
| `OpenAiEmbeddingsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\Embeddings` | `EmbeddingData` |
| `OpenAiFilesResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\DeleteFile` | `array` |
| `OpenAiFilesResource` | `upload()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\UploadFile` | `array` |
| `OpenAiFineTuningResource` | `createJob()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateFineTuningJob` | `array` |
| `OpenAiImagesResource` | `generate()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateImageGeneration` | `array` |
| `OpenAiResource` | `audio()` | `OpenAiAudioResource` | `OpenAiAudioResource` |
| `OpenAiResource` | `chat()` | `OpenAiChatResource` | `OpenAiChatResource` |
| `OpenAiResource` | `embeddings()` | `OpenAiEmbeddingsResource` | `OpenAiEmbeddingsResource` |
| `OpenAiResource` | `files()` | `OpenAiFilesResource` | `OpenAiFilesResource` |
| `OpenAiResource` | `fineTuning()` | `OpenAiFineTuningResource` | `OpenAiFineTuningResource` |
| `OpenAiResource` | `images()` | `OpenAiImagesResource` | `OpenAiImagesResource` |
| `OpenAiResource` | `models()` | `OpenAiModelsResource` | `OpenAiModelsResource` |
| `OpenAiResource` | `responses()` | `OpenAiResponsesResource` | `OpenAiResponsesResource` |
| `OpenAiResource` | `v1()` | `OpenAiV1Resource` | `OpenAiV1Resource` |
| `OpenAiResponsesResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateResponses` | `OpenAiResponseData` |
| `OpenAiV1Resource` | `chatCompletions()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ChatCompletions` | `ChatCompletionData` |
| `OpenAiV1Resource` | `createFineTuningJob()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateFineTuningJob` | `array` |
| `OpenAiV1Resource` | `deleteFile()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1DeleteFile` | `array` |
| `OpenAiV1Resource` | `embeddings()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1Embeddings` | `EmbeddingData` |
| `OpenAiV1Resource` | `imageGenerations()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateImageGeneration` | `array` |
| `OpenAiV1Resource` | `responses()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateResponse` | `OpenAiResponseData` |
| `OpenAiV1Resource` | `speech()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateSpeech` | `array` |
| `OpenAiV1Resource` | `transcriptions()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateTranscription` | `array` |
| `OpenAiV1Resource` | `uploadFile()` | `CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1UploadFile` | `array` |
| `ResourceGroupsResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup` | `ResourceGroupData` |
| `ResourceGroupsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup` | `—` |
| `ResourceGroupsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup` | `ResourceGroupData` |
| `ResourceGroupsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups` | `Collection` |
| `ResourceProvidersResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\GetResourceProvider` | `ResourceProviderData` |
| `ResourceProvidersResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\ListResourceProviders` | `Collection` |
| `ResourceProvidersResource` | `register()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\RegisterResourceProvider` | `ResourceProviderData` |
| `RoleAssignmentsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment` | `RoleAssignmentData` |
| `RoleDefinitionsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\RoleDefinitions\ListRoleDefinitions` | `Collection` |
| `SecretsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret` | `SecretData` |
| `SecretsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret` | `SecretData` |
| `SecretsResource` | `set()` | `CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret` | `SecretData` |
| `ServicePrincipalsResource` | `create()` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\CreateServicePrincipal` | `ServicePrincipalData` |
| `ServicePrincipalsResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\DeleteServicePrincipal` | `—` |
| `ServicePrincipalsResource` | `findByAppIdOrFail()` | `RuntimeException` | `ServicePrincipalData` |
| `ServicePrincipalsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\ListServicePrincipals` | `Collection` |
| `SqlDatabasesResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlDatabase` | `SqlDatabaseData` |
| `SqlDatabasesResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlDatabase` | `—` |
| `SqlDatabasesResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase` | `SqlDatabaseData` |
| `SqlFirewallRulesResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule` | `SqlFirewallRuleData` |
| `SqlFirewallRulesResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlFirewallRule` | `—` |
| `SqlServerResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlServer` | `SqlServerData` |
| `SqlServerResource` | `databases()` | `SqlDatabasesResource` | `SqlDatabasesResource` |
| `SqlServerResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlServer` | `—` |
| `SqlServerResource` | `firewallRules()` | `SqlFirewallRulesResource` | `SqlFirewallRulesResource` |
| `SqlServerResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlServer` | `SqlServerData` |
| `SqlServersResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Sql\ListSqlServersByResourceGroup` | `Collection` |
| `SqlServersResource` | `server()` | `SqlServerResource` | `SqlServerResource` |
| `StorageAccountResource` | `blobContainers()` | `BlobContainersResource` | `BlobContainersResource` |
| `StorageAccountResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageAccount` | `StorageAccountData` |
| `StorageAccountResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\DeleteStorageAccount` | `—` |
| `StorageAccountResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\GetStorageAccount` | `StorageAccountData` |
| `StorageAccountResource` | `listKeys()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountKeys` | `StorageAccountKeysData` |
| `StorageAccountResource` | `regenerateKey()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\RegenerateStorageAccountKey` | `StorageAccountKeysData` |
| `StorageAccountsResource` | `account()` | `StorageAccountResource` | `StorageAccountResource` |
| `StorageAccountsResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountsByResourceGroup` | `Collection` |
| `SubscriptionAliasesResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias` | `SubscriptionAliasData` |
| `SubscriptionAliasesResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\DeleteSubscriptionAlias` | `—` |
| `SubscriptionAliasesResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias` | `SubscriptionAliasData` |
| `SubscriptionsResource` | `cancel()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\CancelSubscription` | `CanceledSubscriptionData` |
| `SubscriptionsResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription` | `SubscriptionData` |
| `UserAssignedIdentityResource` | `createOrUpdate()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\CreateOrUpdateUserAssignedIdentity` | `UserAssignedIdentityData` |
| `UserAssignedIdentityResource` | `delete()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\DeleteUserAssignedIdentity` | `—` |
| `UserAssignedIdentityResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\GetUserAssignedIdentity` | `UserAssignedIdentityData` |
| `UsersResource` | `get()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser` | `UserData` |
| `UsersResource` | `list()` | `CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers` | `Collection` |
| `VaultResource` | `secrets()` | `SecretsResource` | `SecretsResource` |
| `WorkflowRuntimeResource` | `respond()` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RespondToWorkflow` | `array` |
| `WorkflowRuntimeResource` | `run()` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunWorkflow` | `array` |
| `WorkflowRuntimeResource` | `status()` | `CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\GetWorkflowStatus` | `array` |

Generated at: 2026-07-02T11:43:11+00:00
