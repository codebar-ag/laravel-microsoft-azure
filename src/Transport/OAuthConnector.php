<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use Saloon\Http\Connector;

/**
 * @internal
 */
final class OAuthConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://login.microsoftonline.com';
    }
}
