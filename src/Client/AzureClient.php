<?php

namespace CodebarAg\MicrosoftAzure\Client;

use CodebarAg\MicrosoftAzure\Concerns\InteractsWithResources;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Transport\ArmConnector;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use CodebarAg\MicrosoftAzure\Transport\FoundryConnector;
use CodebarAg\MicrosoftAzure\Transport\FunctionRuntimeConnector;
use CodebarAg\MicrosoftAzure\Transport\GraphConnector;
use CodebarAg\MicrosoftAzure\Transport\KeyVaultConnector;
use CodebarAg\MicrosoftAzure\Transport\KuduConnector;
use CodebarAg\MicrosoftAzure\Transport\LogAnalyticsConnector;
use CodebarAg\MicrosoftAzure\Transport\OpenAiConnector;

final class AzureClient
{
    use InteractsWithResources;

    private ?ArmConnector $armConnector = null;

    /** @var array<string, KeyVaultConnector> */
    private array $keyVaultConnectors = [];

    private ?GraphConnector $graphConnector = null;

    private ?LogAnalyticsConnector $logAnalyticsConnector = null;

    /** @var array<string, KuduConnector> */
    private array $kuduConnectors = [];

    /** @var array<string, OpenAiConnector> */
    private array $openAiConnectors = [];

    /** @var array<string, FoundryConnector> */
    private array $foundryConnectors = [];

    /** @var array<string, FunctionRuntimeConnector> */
    private array $functionRuntimeConnectors = [];

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

    public function logAnalyticsConnector(): LogAnalyticsConnector
    {
        return $this->logAnalyticsConnector ??= new LogAnalyticsConnector($this->config, $this->tokens, $this->fetcher);
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

    public function openAiConnector(string $accountName, ?string $apiKey = null): OpenAiConnector
    {
        $cacheKey = $accountName.':'.($apiKey ?? '');

        return $this->openAiConnectors[$cacheKey] ??= new OpenAiConnector(
            $this->config,
            $this->tokens,
            $this->fetcher,
            $accountName,
            $apiKey,
        );
    }

    public function foundryConnector(string $accountName, string $projectName, ?string $apiKey = null): FoundryConnector
    {
        $cacheKey = $accountName.':'.$projectName.':'.($apiKey ?? '');

        return $this->foundryConnectors[$cacheKey] ??= new FoundryConnector(
            $this->config,
            $this->tokens,
            $this->fetcher,
            $accountName,
            $projectName,
            $apiKey,
        );
    }

    public function functionRuntimeConnector(string $appName, ?string $hostKey = null): FunctionRuntimeConnector
    {
        $cacheKey = $appName.':'.($hostKey ?? '');

        return $this->functionRuntimeConnectors[$cacheKey] ??= new FunctionRuntimeConnector(
            $this->config,
            $this->tokens,
            $this->fetcher,
            $appName,
            $hostKey,
        );
    }
}
