<?php

namespace CodebarAg\MicrosoftAzure\Config;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;

/**
 * Resolves the config repository from the current container on every call.
 *
 * The manager is registered as a singleton. Holding on to the config
 * repository that was bound at boot time would leave it reading stale
 * values under Laravel Octane, where each request gets its own sandboxed
 * copy of the configuration.
 *
 * @internal
 */
final class ContainerConfigRepository implements Repository
{
    public function has($key): bool
    {
        return $this->repository()->has($key);
    }

    /**
     * @param  array<array-key, mixed>|string  $key
     */
    public function get($key, $default = null): mixed
    {
        return $this->repository()->get($key, $default);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->repository()->all();
    }

    /**
     * @param  array<array-key, mixed>|string  $key
     */
    public function set($key, $value = null): void
    {
        $this->repository()->set($key, $value);
    }

    public function prepend($key, $value): void
    {
        $this->repository()->prepend($key, $value);
    }

    public function push($key, $value): void
    {
        $this->repository()->push($key, $value);
    }

    private function repository(): Repository
    {
        return Container::getInstance()->make(Repository::class);
    }
}
