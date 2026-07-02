<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum TokenAudience: string
{
    case Arm = 'arm';
    case KeyVault = 'key_vault';
    case Graph = 'graph';
    case Kudu = 'kudu';

    case CognitiveServicesDataPlane = 'cognitive_services_data_plane';

    case FunctionRuntime = 'function_runtime';

    case LogAnalytics = 'log_analytics';

    public function scope(?string $host = null): string
    {
        return match ($this) {
            self::Arm => 'https://management.azure.com/.default',
            self::KeyVault => 'https://vault.azure.net/.default',
            self::Graph => 'https://graph.microsoft.com/.default',
            self::Kudu => 'https://'.rtrim((string) $host, '/').'/.default',
            self::CognitiveServicesDataPlane => 'https://cognitiveservices.azure.com/.default',
            self::FunctionRuntime => 'https://'.rtrim((string) $host, '/').'/.default',
            self::LogAnalytics => 'https://api.loganalytics.io/.default',
        };
    }
}
