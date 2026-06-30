<?php

namespace CodebarAg\MicrosoftAzure\Data\Authentication;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

final class AccessTokenData extends AzureData
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public Carbon $expiresAt,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $expiresIn = Field::int($data, 'expires_in', self::class);

        return new self(
            accessToken: Field::string($data, 'access_token', self::class),
            tokenType: (string) Arr::get($data, 'token_type', 'Bearer'),
            expiresIn: $expiresIn,
            expiresAt: Carbon::now()->addSeconds($expiresIn),
        );
    }
}
