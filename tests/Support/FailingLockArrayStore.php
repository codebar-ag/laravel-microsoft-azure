<?php

namespace CodebarAg\MicrosoftAzure\Tests\Support;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;

/**
 * Cache store whose locks always fail refresh, for token repository edge-case tests.
 */
final class FailingLockArrayStore implements LockProvider, Store
{
    private NonLockingArrayStore $store;

    public function __construct()
    {
        $this->store = new NonLockingArrayStore;
    }

    public function get($key): mixed
    {
        return $this->store->get($key);
    }

    public function many(array $keys): array
    {
        return $this->store->many($keys);
    }

    public function put($key, $value, $seconds): bool
    {
        return $this->store->put($key, $value, $seconds);
    }

    public function putMany(array $values, $seconds): bool
    {
        return $this->store->putMany($values, $seconds);
    }

    public function increment($key, $value = 1): int|false
    {
        return $this->store->increment($key, $value);
    }

    public function decrement($key, $value = 1): int|false
    {
        return $this->store->decrement($key, $value);
    }

    public function forever($key, $value): bool
    {
        return $this->store->forever($key, $value);
    }

    public function forget($key): bool
    {
        return $this->store->forget($key);
    }

    public function flush(): bool
    {
        return $this->store->flush();
    }

    public function getPrefix(): string
    {
        return $this->store->getPrefix();
    }

    public function touch($key, $seconds): bool
    {
        return $this->store->touch($key, $seconds);
    }

    public function lock($name, $seconds = 0, $owner = null): Lock
    {
        return new class implements Lock
        {
            public function get($callback = null): mixed
            {
                return false;
            }

            public function block($seconds, $callback = null): mixed
            {
                return false;
            }

            public function release(): bool
            {
                return true;
            }

            public function owner(): string
            {
                return 'test';
            }

            public function forceRelease(): void {}

            public function restore(): bool
            {
                return true;
            }
        };
    }

    public function restoreLock($name, $owner): Lock
    {
        return $this->lock($name);
    }
}
