<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\KeyVault\SecretData;
use CodebarAg\MicrosoftAzure\Data\KeyVault\SecretIdentifierData;
use CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\ListSecrets;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret;
use Illuminate\Support\Collection;

final class SecretsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $vaultName,
    ) {
        parent::__construct($client);
    }

    public function get(string $secretName, ?string $version = null): SecretData
    {
        $response = $this->sendKeyVault(
            new GetSecret($secretName, $version),
            $this->vaultHost($this->vaultName),
        );

        return SecretData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(string $secretName, string $value, array $attributes = []): SecretData
    {
        $response = $this->sendKeyVault(
            new SetSecret($secretName, new SetSecretPayload($value, $attributes)),
            $this->vaultHost($this->vaultName),
        );

        return SecretData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return Collection<int, SecretIdentifierData>
     */
    public function list(): Collection
    {
        $response = $this->sendKeyVault(
            new ListSecrets,
            $this->vaultHost($this->vaultName),
        );

        return $this->mapList($response, 'value', fn (array $item) => SecretIdentifierData::fromAzure($item));
    }

    public function delete(string $secretName): SecretData
    {
        $response = $this->sendKeyVault(
            new DeleteSecret($secretName),
            $this->vaultHost($this->vaultName),
        );

        return SecretData::fromAzure($this->jsonArray($response));
    }
}
