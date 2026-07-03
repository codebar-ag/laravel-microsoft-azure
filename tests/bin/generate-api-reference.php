#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

/**
 * Generates API reference documentation from inventory, Requests, DTOs, and Resources.
 *
 * Usage:
 *   php tests/bin/generate-api-reference.php [--output=docs/api-reference.md] [--check]
 */
$options = getopt('', ['output:', 'check']);
$outputPath = $options['output'] ?? 'docs/api-reference.md';
$check = array_key_exists('check', $options);

$root = dirname(__DIR__, 2);
$inventoryPath = $root.'/tests/Fixtures/inventory/microsoft-azure-inventory.yaml';
$requestsPath = $root.'/src/Requests';
$dataPath = $root.'/src/Data';
$payloadPath = $root.'/src/Data/Payload';
$resourcesPath = $root.'/src/Resources';

if (! class_exists(Yaml::class)) {
    fwrite(STDERR, "symfony/yaml is required (composer install).\n");
    exit(1);
}

/** @var array<string, array<int, array<string, string>>> $inventory */
$inventory = Yaml::parseFile($inventoryPath)['surfaces'] ?? [];

/**
 * @return array<string, string>
 */
function requestClassToFqcn(string $shortName, string $requestsPath): string
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($requestsPath));

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $contents = file_get_contents($file->getPathname());
        if ($contents !== false && preg_match('/final class '.$shortName.'\b/', $contents)) {
            if (preg_match('/namespace ([^;]+);/', $contents, $ns)) {
                return $ns[1].'\\'.$shortName;
            }
        }
    }

    return $shortName;
}

/**
 * @return list<string>
 */
function reflectPublicFields(string $classFile): array
{
    $contents = file_get_contents($classFile);
    if ($contents === false) {
        return [];
    }

    $fields = [];

    if (preg_match_all('/public\s+(?:readonly\s+)?(?:\??[\w\\\\|]+\s+)?\$(\w+)/', $contents, $matches)) {
        foreach ($matches[1] as $name) {
            if (! in_array($name, $fields, true)) {
                $fields[] = $name;
            }
        }
    }

    return $fields;
}

/**
 * @return array<string, list<string>>
 */
function collectDataClasses(string $dataPath): array
{
    $classes = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dataPath));

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        if (str_contains($path, '/Payload/') || str_contains($path, '/Support/')) {
            continue;
        }

        $contents = file_get_contents($path);
        if ($contents === false || ! preg_match('/(?:final\s+)?class (\w+)/', $contents, $classMatch)) {
            continue;
        }

        if (! preg_match('/namespace ([^;]+);/', $contents, $nsMatch)) {
            continue;
        }

        $shortName = $classMatch[1];
        if ($shortName === 'AzureData') {
            continue;
        }

        $fqcn = $nsMatch[1].'\\'.$shortName;
        $classes[$fqcn] = reflectPublicFields($path);
    }

    ksort($classes);

    return $classes;
}

/**
 * @return array<string, list<string>>
 */
function collectPayloadClasses(string $payloadPath): array
{
    $classes = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($payloadPath));

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $contents = file_get_contents($file->getPathname());
        if ($contents === false || ! preg_match('/final class (\w+)/', $contents, $classMatch)) {
            continue;
        }

        if ($classMatch[1] === 'AzurePayload') {
            continue;
        }

        if (! preg_match('/namespace ([^;]+);/', $contents, $nsMatch)) {
            continue;
        }

        $fqcn = $nsMatch[1].'\\'.$classMatch[1];
        $classes[$fqcn] = reflectPublicFields($file->getPathname());
    }

    ksort($classes);

    return $classes;
}

/**
 * @return array<string, string> payload short name => request short name
 */
function mapPayloadsToRequests(string $requestsPath): array
{
    $map = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($requestsPath));

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $contents = file_get_contents($file->getPathname());
        if ($contents === false || ! preg_match('/final class (\w+)/', $contents, $requestMatch)) {
            continue;
        }

        if (! preg_match('/use CodebarAg\\\\MicrosoftAzure\\\\Data\\\\Payload\\\\(\w+);/', $contents, $payloadMatch)) {
            continue;
        }

        $payload = $payloadMatch[1];
        $request = $requestMatch[1];

        // A payload DTO (e.g. GenericJsonPayload) may be imported by many requests; keep the
        // alphabetically-first request so output is independent of filesystem iteration order.
        if (! isset($map[$payload]) || $request < $map[$payload]) {
            $map[$payload] = $request;
        }
    }

    ksort($map);

    return $map;
}

/**
 * @return list<array{resource: string, method: string, request: string, response: string}>
 */
function collectResourceMappings(string $resourcesPath): array
{
    $rows = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($resourcesPath));

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $contents = file_get_contents($file->getPathname());
        if ($contents === false || ! preg_match('/final class (\w+)/', $contents, $resourceMatch)) {
            continue;
        }

        if ($resourceMatch[1] === 'Resource') {
            continue;
        }

        $resource = $resourceMatch[1];

        if (! preg_match_all(
            '/public function (\w+)\([^)]*\)(?:\s*:\s*(\??[\w\\\\|]+))?[^{]*\{[^}]*?new (\w+)\(/s',
            $contents,
            $matches,
            PREG_SET_ORDER,
        )) {
            continue;
        }

        foreach ($matches as $match) {
            $response = trim(str_replace(['\\CodebarAg\\MicrosoftAzure\\Data\\', 'void'], ['', '—'], $match[2] ?? '—'));
            $rows[] = [
                'resource' => $resource,
                'method' => $match[1],
                'request' => $match[3],
                'response' => $response !== '' ? $response : '—',
            ];
        }
    }

    usort($rows, fn (array $a, array $b) => [$a['resource'], $a['method']] <=> [$b['resource'], $b['method']]);

    return $rows;
}

$dataClasses = collectDataClasses($dataPath);
$payloadClasses = collectPayloadClasses($payloadPath);
$payloadToRequest = mapPayloadsToRequests($requestsPath);
$resourceMappings = collectResourceMappings($resourcesPath);

$markdown = "# Microsoft Azure API reference\n\n";
$markdown .= "Auto-generated reference for Saloon requests, response DTOs, write payloads, and resource gateways.\n\n";
$markdown .= "See also: [inventory parity](inventory-parity.md) for endpoint coverage status.\n\n";

$markdown .= "## HTTP requests\n\n";
$markdown .= "| Surface | Method | Path | Request class |\n";
$markdown .= "| --- | --- | --- | --- |\n";

foreach ($inventory as $surface => $entries) {
    foreach ($entries as $entry) {
        $request = $entry['request'];
        $fqcn = requestClassToFqcn($request, $requestsPath);
        $markdown .= sprintf(
            "| %s | %s | `%s` | `%s` |\n",
            $surface,
            $entry['method'],
            $entry['path'],
            $fqcn,
        );
    }
}

$markdown .= "\n## Response DTOs\n\n";
$markdown .= "| Class | Key fields |\n";
$markdown .= "| --- | --- |\n";

foreach ($dataClasses as $class => $fields) {
    $markdown .= sprintf(
        "| `%s` | %s |\n",
        $class,
        $fields === [] ? '—' : implode(', ', array_map(fn (string $f) => '`'.$f.'`', $fields)),
    );
}

$markdown .= "\n## Request payloads\n\n";
$markdown .= "Write operations accept typed payload DTOs (`toAzureBody()` or `toFormBody()` for OAuth).\n\n";
$markdown .= "| Payload DTO | Request | Fields |\n";
$markdown .= "| --- | --- | --- |\n";

foreach ($payloadClasses as $class => $fields) {
    $shortName = substr($class, strrpos($class, '\\') + 1);
    $request = $payloadToRequest[$shortName] ?? '—';
    $requestFqcn = $request !== '—' ? requestClassToFqcn($request, $requestsPath) : '—';

    $markdown .= sprintf(
        "| `%s` | `%s` | %s |\n",
        $class,
        $requestFqcn,
        $fields === [] ? '—' : implode(', ', array_map(fn (string $f) => '`'.$f.'`', $fields)),
    );
}

$markdown .= "\n**Note:** `ZipDeploy` sends a binary stream body and has no payload DTO.\n\n";

$markdown .= "## Resource gateways\n\n";
$markdown .= "| Resource | Method | Request | Response DTO |\n";
$markdown .= "| --- | --- | --- | --- |\n";

foreach ($resourceMappings as $row) {
    $requestFqcn = requestClassToFqcn($row['request'], $requestsPath);
    $markdown .= sprintf(
        "| `%s` | `%s()` | `%s` | `%s` |\n",
        $row['resource'],
        $row['method'],
        $requestFqcn,
        $row['response'],
    );
}

$markdown .= "\nGenerated at: ".date('c')."\n";

$fullOutputPath = $root.'/'.$outputPath;

$normalize = static function (string $content): string {
    return preg_replace('/\nGenerated at: .+\n?$/', "\n", $content) ?? $content;
};

if ($check) {
    if (! file_exists($fullOutputPath)) {
        fwrite(STDERR, "{$outputPath} is missing — run composer docs:api\n");
        exit(1);
    }

    $existing = file_get_contents($fullOutputPath);
    if ($existing === false || $normalize($existing) !== $normalize($markdown)) {
        fwrite(STDERR, "{$outputPath} is out of date — run composer docs:api\n");
        exit(1);
    }

    echo "Checked {$outputPath} (up to date)\n";
    exit(0);
}

file_put_contents($fullOutputPath, $markdown);

echo "Wrote {$outputPath}\n";

exit(0);
