<?php

namespace CodebarAg\MicrosoftAzure\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class AzureResponseReceived
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>|null  $headers
     */
    public function __construct(
        public string $connection,
        public string $method,
        public string $uri,
        public int $status,
        public ?float $durationMs = null,
        public ?string $requestId = null,
        public ?array $headers = null,
        public ?string $body = null,
    ) {}
}
