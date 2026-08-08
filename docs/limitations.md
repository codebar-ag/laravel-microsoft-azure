# Limitations

Explicit, honest list of what this package deliberately does **not** do.

- **No business logic or orchestration.** This is a thin Saloon-based REST wrapper — typed Request classes and fluent Resource gateways, nothing more. Provisioning sequences, retry/backoff policy beyond the built-in Saloon retry, idempotency across multiple calls, and long-running-operation orchestration beyond the provided polling helpers are the consuming app's responsibility.
- **No official `microsoft/*` Azure SDK dependency.** Every surface is a hand-written Saloon `Request`/DTO pair against the documented REST API, not a wrapped SDK client. See [`ENDPOINTS.md`](../ENDPOINTS.md) for the full endpoint catalog.
- **No .NET SDK or Agent Framework runtime code.** The Foundry Agent Service and Function Runtime surfaces call the HTTP APIs those runtimes expose — they don't embed or replace the Agent Framework `WorkflowBuilder` graph-authoring SDK (.NET/Python only).
- **No database migrations or application seeding.** Deploying a schema into a provisioned Azure SQL database (or similar) is out of scope — bring your own migration runner.
- **No service health checks.** The package makes the calls you ask it to; it doesn't poll third-party "is this service up" endpoints.
- **Actual-state reads are per-resource and control-plane.** `observe()` (see [Advanced usage](advanced.md#actual-state-reads-observe)) issues one ARM/Graph read for one named resource. There is no bulk "what exists in this subscription" sweep — Azure Resource Graph is not wrapped — so a caller reconciling many resources issues many reads. And `Available` is an ARM answer: it means the resource is provisioned, not that its data plane will serve you. `secrets()->observeAccess()` is the only data-plane reachability probe the package ships; other surfaces need their own read.
- **Pre-1.0 / dev-stability.** `composer.json` sets `minimum-stability: dev`; the released line is `v0.x`. Expect breaking changes between minor versions until 1.0. (Note: a `v1.0` tag exists but points at the same commit as `v0.11.0` — it was cut by hand, not by the release workflow, and does not denote a 1.0 API. See [Release tags](#release-tags).)
- **PHP 8.4/8.5 and Laravel 13 only.** No legacy PHP or Laravel support — see [Installation](installation.md).
- **API Management coverage is subscriptions/keys only.** Products, policies, APIs, named values, and users are not covered — see [Logic Apps & API Management](usage/logic-apps-and-apim.md).
- **Foundry Agent Service sub-surfaces vary in maturity.** Memory Stores, Evaluations, Schedules, Datasets, Indexes, and Redteams are lower-priority/preview additions with rougher edges than the core Agents/Responses/Conversations surface — see [Foundry Agent Service](usage/foundry-agent-service.md). Legacy Threads/Runs (Assistants-style) are marked deprecated by Microsoft (sunset August 2026).
- **Response bodies aren't redacted on success paths.** `src/Security/Redactor.php` only scrubs known secret-shaped fields in error summaries. If you log full response bodies (`debug.capture_bodies`), some surfaces can return credential material in a 200 response — see [Configuration → Security](configuration.md#security).
- **A known config-drift bug** currently makes the cache-lifetime and request-timeout env vars no-ops — see [Configuration → Known drift](configuration.md#known-drift-cache-lifetime--request-timeout).

For the design rationale behind the current surface set (why some things were deliberately left out), see [`docs/REFACTOR.md`](REFACTOR.md).

## Release tags

Tags are cut automatically by `.github/workflows/release.yml` (`anothrNick/github-tag-action`, default bump: minor) when a pull request against `main` closes. Two things this does **not** guard against, both currently visible in the tag list:

- **A hand-cut tag.** Composer derives versions from git tags — there is no `version` field in `composer.json` to check a tag against (and there should not be one; Composer's own guidance is to omit it). Nothing verifies that a tag was produced by the workflow, and a tag pushed by hand is installable immediately. `v1.0` and `v0.11.0` both point at commit `b9058b5`; `v0.1` points at a commit that is not an ancestor of `main` at all.
- **An unmerged pull request.** The workflow triggers on `pull_request: types: [closed]` with no `if: github.event.pull_request.merged == true`, so closing a pull request **without** merging it also bumps the version and pushes a tag.

Release by merging a pull request into `main` and letting the workflow tag. Do not push tags by hand.
