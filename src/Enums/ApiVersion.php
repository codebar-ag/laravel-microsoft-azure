<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum ApiVersion
{
    case ARM_RESOURCES;
    case ARM_DEPLOYMENTS;
    case ARM_ROLE_ASSIGNMENTS;
    case ARM_ROLE_DEFINITIONS;
    case ARM_RESOURCE_PROVIDERS;
    case ARM_DELETED_VAULTS;
    case ARM_DELETED_COGNITIVE_SERVICES;
    case ARM_SQL;
    case ARM_POSTGRESQL;
    case ARM_STORAGE;
    case ARM_KEY_VAULT_VAULTS;
    case ARM_LOG_ANALYTICS;
    case ARM_LOGIC;
    case ARM_APP_INSIGHTS;
    case ARM_MANAGED_IDENTITY;
    case ARM_COST_MANAGEMENT;
    case ARM_CONSUMPTION;
    case ARM_MONITOR_METRICS;
    case ARM_SUBSCRIPTIONS;
    case ARM_SUBSCRIPTION_ALIASES;
    case ARM_COGNITIVE_SERVICES;
    case ARM_WEB;
    case ARM_API_MANAGEMENT;
    case OPENAI;
    case OPENAI_PREVIEW;
    case FOUNDRY_AGENTS;
    case FOUNDRY_MEMORY_STORES;
    case KEY_VAULT;
    case STORAGE_QUEUE;
    case GRAPH;

    /**
     * The literal Azure api-version string. Not a backed-enum `value` because
     * several cases legitimately share the same date (same resource provider,
     * different operations) and PHP requires unique values on backed enums.
     */
    public function value(): string
    {
        return match ($this) {
            self::ARM_RESOURCES => '2025-04-01',
            self::ARM_DEPLOYMENTS => '2025-04-01',
            self::ARM_ROLE_ASSIGNMENTS => '2022-04-01',
            self::ARM_ROLE_DEFINITIONS => '2022-04-01',
            self::ARM_RESOURCE_PROVIDERS => '2022-12-01',
            self::ARM_DELETED_VAULTS => '2023-02-01',
            self::ARM_DELETED_COGNITIVE_SERVICES => '2023-05-01',
            self::ARM_SQL => '2025-01-01',
            self::ARM_POSTGRESQL => '2025-08-01',
            self::ARM_STORAGE => '2025-06-01',
            self::ARM_KEY_VAULT_VAULTS => '2026-02-01',
            self::ARM_LOG_ANALYTICS => '2025-02-01',
            self::ARM_LOGIC => '2019-05-01',
            self::ARM_APP_INSIGHTS => '2020-02-02',
            self::ARM_MANAGED_IDENTITY => '2024-11-30',
            self::ARM_COST_MANAGEMENT => '2025-03-01',
            self::ARM_CONSUMPTION => '2024-08-01',
            self::ARM_MONITOR_METRICS => '2023-10-01',
            self::ARM_SUBSCRIPTIONS => '2022-12-01',
            self::ARM_SUBSCRIPTION_ALIASES => '2021-10-01',
            self::ARM_COGNITIVE_SERVICES => '2026-05-01',
            self::ARM_WEB => '2024-11-01',
            self::ARM_API_MANAGEMENT => '2022-08-01',
            self::OPENAI => '2024-10-21',
            self::OPENAI_PREVIEW => '2025-04-01-preview',
            self::FOUNDRY_AGENTS => 'v1',
            self::FOUNDRY_MEMORY_STORES => '2025-11-15-preview',
            self::KEY_VAULT => '2025-07-01',
            self::STORAGE_QUEUE => '2025-05-05',
            self::GRAPH => 'v1.0',
        };
    }
}
