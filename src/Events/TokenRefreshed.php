<?php

namespace CodebarAg\MicrosoftAzure\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class TokenRefreshed
{
    use Dispatchable;

    public function __construct(
        public string $connection,
        public string $tenantId,
        public string $clientId,
    ) {}
}
