# Changelog

All notable changes to `laravel-microsoft-azure` are documented here. This project follows [Semantic Versioning](https://semver.org/); tags are cut automatically on merge to `main` (see `.github/workflows/release.yml`).

## [Unreleased]

### Added
- Azure API Management (APIM): create/get/delete an API Management service, and create/get/list subscriptions, list/regenerate subscription keys, update subscription state.
- Storage Queue data-plane support: create/delete queue, send/receive/delete messages, with both Shared Key and Entra ID (OAuth) authentication.
- Expanded Azure AI Foundry Agent Service surface: agent container operations, agent replace and version routing, plus new Connections, Conversations, Datasets, Evaluations, Indexes, Memory Stores, Redteams, Responses, Schedules, Schedule Runs, Skills, and Toolboxes resources.
- XML response parsing (`Data\Support\XmlField`) and a JSON-object body helper (`Concerns\SendsJsonObjectBody`) for endpoints that require an explicit `{}` body.
- Optional `identityType` parameter for Cognitive Services account and Foundry project creation.
- Optional `agentVersion` scoping on Foundry-scoped resources.

### Fixed
- `CancelSubscription` now sends `api-version=2022-12-01` (`ApiVersion::ARM_SUBSCRIPTIONS`) instead of the `SubscriptionAliases` api-version it was mistakenly copied from, matching its `GetSubscription`/`ListSubscriptions` siblings.

### Changed (BREAKING)
- `Enums\ApiVersion` is now a native PHP enum instead of a plain class of public string constants, for consistency with every other member of `Enums/`. The literal api-version string is no longer available as `ApiVersion::ARM_STORAGE` directly (that now yields an enum case) — call `->value()` to get the string, e.g. `ApiVersion::ARM_STORAGE->value()`. It's a method rather than the built-in backed-enum `->value` property because several cases legitimately share the same date (same ARM resource provider, different operations), which PHP's backed-enum uniqueness rule doesn't allow.
  - **Migration:** anywhere code reads `ApiVersion::SOME_CASE` expecting a string (e.g. building a query array manually), append `->value()`.
  - This requires a major version bump — `release.yml`'s auto-tagger defaults to a minor bump on merge, so the next release from this branch should be tagged manually as a major.

## [v0.4.0] - 2026-07-03

### Added
- `Sql` token audience for Azure SQL AAD-token authentication.

## [v0.3.0] - 2026-07-02

### Added
- Azure Logic Apps support: workflows, runs, run actions, triggers, trigger histories, versions, and callback URLs.
- Log Analytics query support.
- OpenAI v1 resource surface.

## [v0.2.0] - 2026-07-01

### Added
- Resource Manager resource providers and role definitions.
- Foundry Agent Service: hosted/workflow agent definitions, agent endpoints, agent versions, RAI config.
- `FoundryFeature` and `AgentKind` enums.

## [v0.1.1] - 2026-07-01

### Fixed
- Test suite fixes and improvements. No public API changes.

## [v0.1] - 2026-07-01

Initial release.
