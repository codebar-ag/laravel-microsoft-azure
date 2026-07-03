<?php

namespace CodebarAg\MicrosoftAzure\Data;

use ReflectionClass;
use Spatie\LaravelData\Data;

/**
 * Base for every Azure response/value object.
 */
abstract class AzureData extends Data
{
    public function copyWith(mixed ...$overrides): static
    {
        $reflection = new ReflectionClass($this);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return clone $this;
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();
            $args[] = array_key_exists($name, $overrides)
                ? $overrides[$name]
                : $this->{$name};
        }

        return $reflection->newInstanceArgs($args);
    }
}
