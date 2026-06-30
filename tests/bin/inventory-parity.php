#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

/**
 * Verifies that every inventory endpoint has a matching Request class.
 *
 * Usage:
 *   php tests/bin/inventory-parity.php [--output=docs/inventory-parity.md] [--check]
 */
$options = getopt('', ['output:', 'check']);
$outputPath = $options['output'] ?? 'docs/inventory-parity.md';
$check = array_key_exists('check', $options);

$root = dirname(__DIR__, 2);
$inventoryPath = $root.'/tests/Fixtures/inventory/microsoft-azure-inventory.yaml';
$requestsPath = $root.'/src/Requests';

if (! class_exists(Yaml::class)) {
    fwrite(STDERR, "symfony/yaml is required (composer install).\n");
    exit(1);
}

/** @var array<string, array<int, array<string, string>>> $inventory */
$inventory = Yaml::parseFile($inventoryPath)['surfaces'] ?? [];

/** @var array<string, true> $requestClasses */
$requestClasses = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($requestsPath));

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $contents = file_get_contents($file->getPathname());
    if ($contents === false || ! preg_match('/final class (\w+)/', $contents, $matches)) {
        continue;
    }

    $requestClasses[$matches[1]] = true;
}

$rows = [];
$missingRequired = 0;

foreach ($inventory as $surface => $entries) {
    foreach ($entries as $entry) {
        $request = $entry['request'];
        $tier = $entry['tier'] ?? 'required';
        $exists = isset($requestClasses[$request]);

        $status = match (true) {
            $tier === 'internal' => $exists ? 'Internal' : 'MissingInternal',
            ! $exists => 'Missing',
            default => 'Parity',
        };

        if ($status === 'Missing' && $tier === 'required') {
            $missingRequired++;
        }

        $rows[] = [
            'surface' => $surface,
            'method' => $entry['method'],
            'path' => $entry['path'],
            'request' => $request,
            'tier' => $tier,
            'status' => $status,
        ];
    }
}

$markdown = "# Microsoft Azure endpoint inventory parity\n\n";
$markdown .= "| Surface | Method | Path | Request | Tier | Status |\n";
$markdown .= "| --- | --- | --- | --- | --- | --- |\n";

foreach ($rows as $row) {
    $markdown .= sprintf(
        "| %s | %s | %s | %s | %s | %s |\n",
        $row['surface'],
        $row['method'],
        $row['path'],
        $row['request'],
        $row['tier'],
        $row['status'],
    );
}

$markdown .= "\nGenerated at: ".date('c')."\n";

file_put_contents($root.'/'.$outputPath, $markdown);

echo "Wrote {$outputPath}\n";

if ($check && $missingRequired > 0) {
    fwrite(STDERR, "Missing {$missingRequired} required request class(es).\n");
    exit(1);
}

exit(0);
