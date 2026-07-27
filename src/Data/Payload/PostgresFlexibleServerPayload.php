<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * Create/update body for `Microsoft.DBforPostgreSQL/flexibleServers`.
 *
 * Unlike Azure SQL (see {@see SqlServerPayload}) the SKU is a **top-level
 * object**, not a `properties` member, and it is required on create — a
 * flexible server has no default tier. `tier` must match the family the
 * `name` belongs to (`Standard_B1ms` → `Burstable`,
 * `Standard_D2ds_v5` → `GeneralPurpose`, `Standard_E2ds_v5` → `MemoryOptimized`);
 * a mismatch is rejected by ARM rather than corrected.
 *
 * `administratorLoginPassword` is write-only — Azure never echoes it back on
 * a GET, so a caller diffing a fetched server against this payload will
 * always see it as "changed".
 */
final class PostgresFlexibleServerPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties  merged over the defaults below, so a caller can
     *                                            set `network`, `backup`, `highAvailability`,
     *                                            `authConfig`, … without this class enumerating them
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $skuName,
        public readonly string $skuTier,
        public readonly ?string $administratorLogin = null,
        public readonly ?string $administratorLoginPassword = null,
        public readonly ?string $version = null,
        public readonly ?int $storageSizeGB = null,
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $properties = $this->properties;

        if ($this->administratorLogin !== null) {
            $properties['administratorLogin'] = $this->administratorLogin;
        }

        if ($this->administratorLoginPassword !== null) {
            $properties['administratorLoginPassword'] = $this->administratorLoginPassword;
        }

        if ($this->version !== null) {
            $properties['version'] = $this->version;
        }

        if ($this->storageSizeGB !== null) {
            // Merged rather than assigned so a caller passing a fuller
            // `storage` block (autoGrow, tier, throughput) keeps it.
            $storage = is_array($properties['storage'] ?? null) ? $properties['storage'] : [];
            $properties['storage'] = $storage + ['storageSizeGB' => $this->storageSizeGB];
        }

        $body = [
            'location' => $this->location,
            'sku' => [
                'name' => $this->skuName,
                'tier' => $this->skuTier,
            ],
            'properties' => $properties,
        ];

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
