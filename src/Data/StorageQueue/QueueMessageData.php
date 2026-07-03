<?php

namespace CodebarAg\MicrosoftAzure\Data\StorageQueue;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class QueueMessageData extends AzureData
{
    public function __construct(
        public string $messageId,
        public ?string $popReceipt = null,
        public ?string $insertionTime = null,
        public ?string $expirationTime = null,
        public ?string $timeNextVisible = null,
        public ?int $dequeueCount = null,
        public ?string $messageText = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            messageId: Field::optionalString($data, 'MessageId'),
            popReceipt: Field::nullableString($data, 'PopReceipt'),
            insertionTime: Field::nullableString($data, 'InsertionTime'),
            expirationTime: Field::nullableString($data, 'ExpirationTime'),
            timeNextVisible: Field::nullableString($data, 'TimeNextVisible'),
            dequeueCount: Field::nullableInt($data, 'DequeueCount'),
            messageText: Field::nullableString($data, 'MessageText'),
        );
    }
}
