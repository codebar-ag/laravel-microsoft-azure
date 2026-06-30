<?php

namespace CodebarAg\MicrosoftAzure\Tests\Support;

use Saloon\Http\Faking\Fixture;

final class MicrosoftAzureFixture extends Fixture
{
    /**
     * @return array<string, string>
     */
    protected function defineSensitiveHeaders(): array
    {
        return [
            'Authorization' => 'Bearer REDACTED',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            'access_token' => 'REDACTED',
            'client_secret' => 'REDACTED',
            'value' => 'REDACTED',
        ];
    }
}
