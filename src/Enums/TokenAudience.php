<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum TokenAudience: string
{
    case Arm = 'arm';
    case KeyVault = 'key_vault';
    case Graph = 'graph';
    case Kudu = 'kudu';

    public function scope(?string $host = null): string
    {
        return match ($this) {
            self::Arm => 'https://management.azure.com/.default',
            self::KeyVault => 'https://vault.azure.net/.default',
            self::Graph => 'https://graph.microsoft.com/.default',
            self::Kudu => 'https://'.rtrim((string) $host, '/').'/.default',
        };
    }
}
