# Installation

## Requirements

| | Supported |
|---|---|
| PHP | `8.4.*` or `8.5.*` |
| Laravel (`illuminate/contracts`, `illuminate/support`) | `^13.0` |

No older PHP or Laravel versions are supported — `composer.json` pins `php": "8.4.*|8.5.*"` and `illuminate/contracts`/`illuminate/support` to `^13.0` only, and the CI matrix (`.github/workflows/run-tests.yml`) only tests PHP 8.4/8.5 against Laravel 13.

The package is pre-1.0 (`minimum-stability: dev`, currently `v0.4.x`) — expect breaking changes between minor versions until a `v1.0.0` is tagged. Check [`UPGRADING.md`](../UPGRADING.md) before bumping.

## Install

```bash
composer require codebar-ag/laravel-microsoft-azure
```

The service provider and `Azure` facade are registered automatically via Laravel package auto-discovery (`composer.json`'s `extra.laravel` block) — no manual registration needed in `config/app.php` or a bootstrap file.

## Publish the config file

```bash
php artisan vendor:publish --tag=laravel-microsoft-azure-config
```

This creates `config/laravel-microsoft-azure.php`. See [Configuration](configuration.md) for every key it supports.

## Azure prerequisites

You need an Azure AD (Entra ID) app registration with a client secret, and — depending on which surfaces you call — RBAC role assignments granting that app registration access to the resources it will manage:

- An **app registration** (service principal) with a client secret, to drive the OAuth2 client-credentials flow the package uses for every service.
- **RBAC role assignments** scoped to whatever you call: e.g. `Contributor` on a subscription for ARM resource management, `Storage Queue Data Contributor` for Storage Queue (Entra path), a Key Vault access policy or RBAC role for secrets, `Enrollment account subscription creator` (or equivalent) for billing/subscription-alias operations. See [docs/troubleshooting.md](troubleshooting.md) for the 403 failure mode this produces when a role is missing.

> **Note (needs maintainer input):** the package does not itself provision the app registration or role assignments — that's assumed to already exist. Nothing in the repo prescribes a minimal/recommended RBAC role set, so treat the roles above as "grant what the surfaces you use require," not an exhaustive least-privilege list.
