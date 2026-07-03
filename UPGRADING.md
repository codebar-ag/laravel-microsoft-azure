# Upgrading

Breaking changes are documented here as they land in [`CHANGELOG.md`](CHANGELOG.md), grouped by the release that introduces them.

## [Unreleased] → next major

### `Enums\ApiVersion` is now a native PHP enum

`ApiVersion` used to be a class of public string constants. It's now a native PHP enum, for consistency with every other member of `Enums/`.

```php
// Before
$apiVersion = ApiVersion::ARM_STORAGE; // string, e.g. "2023-01-01"

// After
$apiVersion = ApiVersion::ARM_STORAGE;          // enum case, not a string
$apiVersion = ApiVersion::ARM_STORAGE->value(); // "2023-01-01" — call ->value() to get the string
```

`->value()` is a method rather than the built-in backed-enum `->value` property because several cases legitimately share the same date string (same ARM resource provider, different operations) — PHP's backed-enum case-value uniqueness rule doesn't allow that, so the enum is unbacked and `value()` does the lookup via `match`.

**Migration steps:** search your application for any code that reads `ApiVersion::SOME_CASE` and treats the result as a string directly (e.g. manually building a query array with an api-version). Append `->value()` at each call site. If you only ever pass `ApiVersion::X` into this package's own methods — which already expect the enum case, not a string — no changes are needed.

> **Maintainer action required:** this change is still in `[Unreleased]` — the package has only shipped `v0.1`–`v0.4.0` so far, all of which predate it. `.github/workflows/release.yml`'s auto-tagger defaults to a **minor** version bump on merge to `main`. Because this is a breaking change, the next release built from this branch needs a **manual major-version override** at merge/tag time, not the default minor bump.

<!-- Future breaking changes append below this line, newest first. -->
