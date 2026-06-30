<?php

namespace CodebarAg\MicrosoftAzure\Tests\Support;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\InteractsWithTime;

/**
 * Minimal cache store for exercising non-locking token refresh paths in tests.
 */
final class NonLockingArrayStore implements Store
{
    use InteractsWithTime;

    /** @var array<string, array{value: mixed, expiresAt: int}> */
    private array $storage = [];

    public function get($key): mixed
    {
        if (! isset($this->storage[$key])) {
            return null;
        }

        if ($this->currentTime() >= $this->storage[$key]['expiresAt']) {
            unset($this->storage[$key]);

            return null;
        }

        return $this->storage[$key]['value'];
    }

    public function many(array $keys): array
    {
        return array_combine($keys, array_map(fn (string $key) => $this->get($key), $keys));
    }

    public function put($key, $value, $seconds): bool
    {
        $this->storage[$key] = [
            'value' => $value,
            'expiresAt' => $this->availableAt($seconds),
        ];

        return true;
    }

    public function putMany(array $values, $seconds): bool
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $seconds);
        }

        return true;
    }

    public function increment($key, $value = 1): int|false
    {
        $current = (int) ($this->get($key) ?? 0);
        $current += $value;
        $this->put($key, $current, 3600);

        return $current;
    }

    public function decrement($key, $value = 1): int|false
    {
        return $this->increment($key, $value * -1);
    }

    public function forever($key, $value): bool
    {
        return $this->put($key, $value, 315360000);
    }

    public function forget($key): bool
    {
        unset($this->storage[$key]);

        return true;
    }

    public function flush(): bool
    {
        $this->storage = [];

        return true;
    }

    public function getPrefix(): string
    {
        return 'non-locking:';
    }

    public function touch($key, $seconds): bool
    {
        if (! isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key]['expiresAt'] = $this->availableAt($seconds);

        return true;
    }
}
