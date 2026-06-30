<?php

namespace CodebarAg\MicrosoftAzure\Data\Support;

use Illuminate\Support\Arr;
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
    public static function optionalString(array $data, string $key, string $default = ''): string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : null;
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

    /**
     * @param  array<string, mixed>  $data
     */
    public static function optionalInt(array $data, string $key, int $default): int
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function bool(array $data, string $key, bool $default = false): bool
    {
        $value = $data[$key] ?? null;

        return is_bool($value) ? $value : $default;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function arrString(array $data, string $key, string $default = ''): string
    {
        $value = Arr::get($data, $key, $default);

        return is_string($value) ? $value : $default;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function arrNullableString(array $data, string $key): ?string
    {
        $value = Arr::get($data, $key);

        return is_string($value) ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function mixedArray(array $data, string $key): array
    {
        $value = $data[$key] ?? [];

        return is_array($value) ? self::stringKeyArray($value) : [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function properties(array $data): array
    {
        return self::mixedArray($data, 'properties');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    public static function stringList(array $data, string $key): array
    {
        $value = $data[$key] ?? [];

        if (! is_array($value)) {
            return [];
        }

        $strings = [];

        foreach ($value as $item) {
            if (is_string($item)) {
                $strings[] = $item;
            }
        }

        return $strings;
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromJson(mixed $json): array
    {
        if (! is_array($json)) {
            return [];
        }

        return self::stringKeyArray($json);
    }

    /**
     * @param  array<mixed, mixed>  $array
     * @return array<string, mixed>
     */
    public static function stringKeyArray(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
