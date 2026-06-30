<?php

namespace CodebarAg\MicrosoftAzure\Data\Support;

use InvalidArgumentException;

final class Field
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function string(array $data, string $key, string $class): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException("Missing or invalid [{$key}] in {$class}.");
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function int(array $data, string $key, string $class): int
    {
        $value = $data[$key] ?? null;

        if (! is_numeric($value)) {
            throw new InvalidArgumentException("Missing or invalid [{$key}] in {$class}.");
        }

        return (int) $value;
    }
}
