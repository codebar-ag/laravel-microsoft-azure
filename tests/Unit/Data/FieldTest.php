<?php

use CodebarAg\MicrosoftAzure\Data\Support\Field;

it('reads required string and int fields', function (): void {
    expect(Field::string(['name' => 'rg-test'], 'name', 'Test'))->toBe('rg-test')
        ->and(Field::int(['count' => '42'], 'count', 'Test'))->toBe(42);
});

it('throws when required fields are missing', function (): void {
    expect(fn () => Field::string([], 'name', 'Test'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Field::int([], 'count', 'Test'))->toThrow(InvalidArgumentException::class);
});

it('reads optional and nullable field helpers', function (): void {
    expect(Field::optionalString(['a' => 'x'], 'a', 'default'))->toBe('x')
        ->and(Field::optionalString([], 'a', 'default'))->toBe('default')
        ->and(Field::nullableString(['a' => 'x'], 'a'))->toBe('x')
        ->and(Field::nullableString([], 'a'))->toBeNull()
        ->and(Field::optionalInt(['n' => '5'], 'n', 1))->toBe(5)
        ->and(Field::optionalInt([], 'n', 1))->toBe(1);
});

it('normalizes json and nested arrays', function (): void {
    expect(Field::fromJson(['id' => '1', 0 => 'skip']))->toBe(['id' => '1'])
        ->and(Field::fromJson(null))->toBe([])
        ->and(Field::mixedArray(['tags' => ['env' => 'test']], 'tags'))->toBe(['env' => 'test'])
        ->and(Field::stringList(['types' => ['Unified', 1]], 'types'))->toBe(['Unified'])
        ->and(Field::stringList(['types' => 'not-a-list'], 'types'))->toBe([])
        ->and(Field::arrString(['properties' => ['scope' => '/sub']], 'properties.scope'))->toBe('/sub')
        ->and(Field::bool(['enabled' => true], 'enabled'))->toBeTrue()
        ->and(Field::bool([], 'enabled', true))->toBeTrue()
        ->and(Field::bool(['enabled' => 'yes'], 'enabled'))->toBeFalse();
});
