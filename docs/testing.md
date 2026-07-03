# Testing

> This page is about testing **this package itself** (for contributors). If you're looking for how to fake/mock this package's Azure calls in your own application's tests, see the note at the bottom.

```bash
composer test              # offline unit + core tests (CI)
composer test:coverage     # 100% line coverage on src/ (CI, requires pcov)
composer test:live         # live Azure integration (requires credentials)
composer test:record       # live run with fixture recording enabled
composer inventory:parity  # endpoint coverage report
composer docs:api          # regenerate API reference
composer analyse           # PHPStan level 10
composer format             # Pint
```

CI runs **PHPStan level 10**, **100% unit test coverage** (offline Saloon fixtures), and **live integration tests** when `MICROSOFT_AZURE_*` GitHub secrets are configured.

Set `MICROSOFT_AZURE_TENANT_ID`, `MICROSOFT_AZURE_CLIENT_ID`, `MICROSOFT_AZURE_CLIENT_SECRET`, and `MICROSOFT_AZURE_SUBSCRIPTION_ID` in gitignored `phpunit.xml` (copy from `phpunit.xml.dist` and fill the empty placeholders — never commit real secrets). CI passes the same vars via GitHub Actions secrets.

Integration tests provision their own resource groups via the API and tear them down after each test. Optionally override the Azure region with `MICROSOFT_AZURE_TESTS_LOCATION` (default: `westeurope`).

The full live/record suite can take several minutes — provisioning a real API Management service alone commonly takes 2–3 minutes. `composer.json` sets `config.process-timeout` to 1800s to accommodate this; if you invoke `vendor/bin/pest` directly (bypassing Composer's script runner) instead of `composer test:live`/`test:record`, that ceiling doesn't apply and you won't need to worry about it either way.

The service principal needs **Contributor** (or equivalent write/read roles) on `MICROSOFT_AZURE_SUBSCRIPTION_ID` for standard-tier integration tests. Tests skip gracefully with a clear message when OAuth succeeds but RBAC is insufficient.

`FoundryAgentServiceTest` additionally needs a data-plane role (e.g. **Azure AI User**/**Azure AI Developer**) on the Cognitive Services account/project — plain subscription `Contributor` isn't enough for `agents()->create()` and similar data-plane calls, and Azure doesn't auto-grant it outside Portal-driven creation. The test grants this itself and retries for up to 5 minutes waiting for propagation; if it still fails, verify the role assignment actually landed (Azure Portal → the account/project → Access control (IAM)) rather than assuming it's a code bug. Similarly, `StorageQueueTest`'s Entra ID (OAuth) path needs **Storage Queue Data Contributor** on the storage account — its Shared Key path is asserted as a hard requirement and doesn't need this role, so a skip there specifically means the OAuth path lacks RBAC, not that the package is broken.

## Saloon fixtures

Offline tests replay redacted HTTP fixtures from `tests/Fixtures/saloon/`. After a green live run with Contributor access, record or refresh fixtures:

```bash
composer test:record
./vendor/bin/pint
composer test   # verify offline replay still passes
```

Set `MICROSOFT_AZURE_RECORD_FIXTURES=true` (as `test:record` does) to write fixtures during integration tests. Secrets in responses are redacted automatically.

Live recordings for ARM requests live under `tests/Fixtures/saloon/live/` (gitignored — regenerated per run, not committed) and are kept separate from the small set of hand-crafted fixtures committed at the top level of `tests/Fixtures/saloon/` for offline unit tests (e.g. `get-resource-group.json`, `get-subscription-alias.json`). Don't point a live-recording mock at one of those committed fixture names — reuse the `liveFixture()` helper in `tests/Pest.php`, which namespaces under `live/` and deletes any existing recording first so `test:record` actually refreshes stale data instead of silently replaying it.

## Live integration tiers

| Tier | Required env | Tests |
|------|----------------|-------|
| Standard | OAuth + subscription ID | Resource group create/get/list/delete; subscription list/get |
| Billing | above + `MICROSOFT_AZURE_TESTS_BILLING_SCOPE` | Subscription alias create/update/list/get; cancel on newly created subscription |

## Billing scope setup

Billing scope is the ARM resource ID of your enrollment account. Alias lifecycle tests skip when `MICROSOFT_AZURE_TESTS_BILLING_SCOPE` is unset.

1. Azure Portal → **Cost Management + Billing** → **Billing accounts**
2. Open your account → **Enrollment accounts** (MCA) or the invoice section path for your agreement type
3. Copy the **Resource ID** — format like `/providers/Microsoft.Billing/billingAccounts/{id}/enrollmentAccounts/{id}`
4. Grant the service principal **Enrollment account subscription creator** (or equivalent billing write role)

```env
MICROSOFT_AZURE_TESTS_BILLING_SCOPE=/providers/Microsoft.Billing/billingAccounts/{billingAccountName}/enrollmentAccounts/{enrollmentAccountName}
```

Teardown cancels the newly created subscription and deletes the alias (best-effort).

## Testing your own application's use of this package

> **Note (needs maintainer input):** nothing package-specific is provided for consumers to mock/fake calls in their own app's test suite. Since every Request is a standard Saloon `Request` class, Saloon's own testing tools — `MockClient::global()`, `Saloon::fake()`, and per-connector fixture recording — should work against this package's Connectors and Requests the same way they would for any Saloon-based integration. This hasn't been verified from inside this repo (the package's own tests exercise it as a Saloon *provider*, not as a dependency consumed by another app), so treat it as a starting point rather than confirmed guidance.
