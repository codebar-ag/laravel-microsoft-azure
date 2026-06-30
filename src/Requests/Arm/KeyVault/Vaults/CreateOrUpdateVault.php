<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults;

use CodebarAg\MicrosoftAzure\Data\Payload\KeyVaultPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateVault extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $vaultName,
        public readonly KeyVaultPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.KeyVault/vaults/'.$this->vaultName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_KEY_VAULT_VAULTS];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
