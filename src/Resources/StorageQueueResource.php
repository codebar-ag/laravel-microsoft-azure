<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\StorageQueue\QueueMessageData;
use CodebarAg\MicrosoftAzure\Data\Support\XmlField;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\DeleteMessage;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\ReceiveMessages;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\SendMessage;
use Illuminate\Support\Collection;

final class StorageQueueResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $accountName,
        private readonly string $queueName,
        private readonly ?string $accountKey = null,
    ) {
        parent::__construct($client);
    }

    public function sendMessage(
        string $body,
        ?int $visibilityTimeoutSeconds = null,
        ?int $messageTtlSeconds = null,
    ): QueueMessageData {
        $response = $this->sendStorageQueue(
            new SendMessage($this->queueName, base64_encode($body), $visibilityTimeoutSeconds, $messageTtlSeconds),
            $this->accountName,
            $this->accountKey,
        );

        $rows = XmlField::elements((string) $response->body(), 'QueueMessage');

        return QueueMessageData::fromAzure($rows[0] ?? []);
    }

    /**
     * @return Collection<int, QueueMessageData>
     */
    public function receiveMessages(int $numberOfMessages = 1, ?int $visibilityTimeoutSeconds = null): Collection
    {
        $response = $this->sendStorageQueue(
            new ReceiveMessages($this->queueName, $numberOfMessages, $visibilityTimeoutSeconds),
            $this->accountName,
            $this->accountKey,
        );

        $rows = XmlField::elements((string) $response->body(), 'QueueMessage');

        return new Collection(array_map(fn (array $row) => QueueMessageData::fromAzure($row), $rows));
    }

    public function deleteMessage(string $messageId, string $popReceipt): void
    {
        $this->sendStorageQueue(
            new DeleteMessage($this->queueName, $messageId, $popReceipt),
            $this->accountName,
            $this->accountKey,
        );
    }
}
