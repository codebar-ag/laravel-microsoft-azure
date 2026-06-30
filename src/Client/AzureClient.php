<?php

namespace CodebarAg\MicrosoftAzure\Client;

use CodebarAg\MicrosoftAzure\Concerns\InteractsWithResources;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Transport\ArmConnector;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use CodebarAg\MicrosoftAzure\Transport\GraphConnector;
use CodebarAg\MicrosoftAzure\Transport\KeyVaultConnector;
use CodebarAg\MicrosoftAzure\Transport\KuduConnector;

final class AzureClient
{
    use InteractsWithResources;

    private ?ArmConnector $armConnector = null;

    /** @var array<string, KeyVaultConnector> */
    private array $keyVaultConnectors = [];

    private ?GraphConnector $graphConnector = null;

    /** @var array<string, KuduConnector> */
    private array $kuduConnectors = [];

    public function __construct(
        public readonly ConnectionConfig $config,
        private readonly TokenRepository $tokens,
        private readonly ClientCredentialsTokenFetcher $fetcher,
    ) {}

    public function name(): string
    {
        return $this->config->name;
    }

    protected function resourceClient(): self
    {
        return $this;
    }

    public function arm(): ArmConnector
    {
        return $this->armConnector ??= new ArmConnector($this->config, $this->tokens, $this->fetcher);
    }

    public function keyVault(string $host): KeyVaultConnector
    {
        return $this->keyVaultConnectors[$host] ??= new KeyVaultConnector(
            $this->config,
            $this->tokens,
            $this->fetcher,
            $host,
        );
    }

    public function graph(): GraphConnector
    {
        return $this->graphConnector ??= new GraphConnector($this->config, $this->tokens, $this->fetcher);
    }

    public function kudu(string $appName): KuduConnector
    {
        return $this->kuduConnectors[$appName] ??= new KuduConnector(
            $this->config,
            $this->tokens,
            $this->fetcher,
            $appName,
        );
    }
}
