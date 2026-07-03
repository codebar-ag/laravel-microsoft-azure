# Foundry & Azure OpenAI

Two layers: the ARM **control plane** (Cognitive Services/AI Foundry accounts, model deployments, projects) and the **data plane** (Azure OpenAI inference). The typical flow is control-plane first (create the account, deploy a model) then data-plane (call it). For the Agent Service / project-scoped data plane, see [Foundry Agent Service](foundry-agent-service.md). Full REST path tables: [`ENDPOINTS.md` §2](../../ENDPOINTS.md#2-foundry-control-plane-arm) and [§6](../../ENDPOINTS.md#6-azure-openai-data-plane).

## Control plane — accounts, deployments, projects (ARM, api-version `2026-05-01`)

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$cs = Azure::instance()->cognitiveServices($subscriptionId, 'my-rg');

$cs->account('my-aif')->createOrUpdate(
    location: 'westeurope',
    properties: [
        'customSubDomainName' => 'my-aif',
        'disableLocalAuth' => false,
        'allowProjectManagement' => true,
    ],
    identityType: 'SystemAssigned', // required by Azure to create projects on this account
);

$keys = $cs->account('my-aif')->listKeys();
$cs->account('my-aif')->regenerateKey('Key1');

$cs->account('my-aif')->deployments()->createOrUpdate(
    deploymentName: 'gpt-5-mini',
    modelFormat: 'OpenAI',
    modelName: 'gpt-5-mini',
    modelVersion: '2025-08-07',
    skuName: 'GlobalStandard',
    skuCapacity: 10,
);

$cs->account('my-aif')->projects()->createOrUpdate('my-prj', 'westeurope');
```

Soft-deleted accounts (purge/list) follow the same pattern as [deleted Key Vaults](key-vault.md#soft-deleted-vaults) — see `Azure::instance()->deletedCognitiveServices($subscriptionId)`.

## Data plane — Azure OpenAI inference (dated surface)

```php
$openai = Azure::instance()->openAi('my-aif'); // optional 2nd arg: api key, instead of Entra

$openai->chat()->create('gpt-5-mini', [
    'messages' => [['role' => 'user', 'content' => 'Hello']],
]);

$openai->embeddings()->create('embed-model', ['input' => 'text']);
$openai->models()->list();
$openai->responses()->create(['model' => 'gpt-5-mini', 'input' => 'Hello']);
```

Also available on the dated surface: speech/transcription (`audio/speech`, `audio/transcriptions`), image generation, file upload/list/delete, and fine-tuning jobs — see `ENDPOINTS.md` §6 for the full path list.

## Data plane — OpenAI v1 (GA, unversioned)

GA since August 2025. Paths live under unversioned `/openai/v1/*` — no `api-version` query parameter and no `/deployments/{id}` path segment. The target model is passed in the request body (`model` field) instead. The dated surface above is unchanged and remains available side by side.

```php
$v1 = Azure::instance()->openAi('my-aif')->v1();

$v1->chatCompletions([
    'model' => 'gpt-5-mini',
    'messages' => [['role' => 'user', 'content' => 'Hello']],
]);

$v1->embeddings(['model' => 'embed-model', 'input' => 'text']);
$v1->responses(['model' => 'gpt-5-mini', 'input' => 'Hello']);
$v1->models();
```

For the distinct, project-scoped `foundry(...)->responses()` surface (Foundry Agent Service), see [Foundry Agent Service](foundry-agent-service.md).
