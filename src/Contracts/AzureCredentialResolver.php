<?php

namespace CodebarAg\MicrosoftAzure\Contracts;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;

interface AzureCredentialResolver
{
    public function resolve(string $connectionName): ConnectionConfig;
}
