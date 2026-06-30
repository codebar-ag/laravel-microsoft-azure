<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Data\AzureData;

abstract class AzurePayload extends AzureData
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toAzureBody(): array;
}
