<?php

namespace CodebarAg\MicrosoftAzure\Requests\StorageQueue;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteMessage extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $queueName,
        public readonly string $messageId,
        public readonly string $popReceipt,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.$this->queueName.'/messages/'.$this->messageId;
    }

    /** @return array<string, string> */
    protected function defaultQuery(): array
    {
        return ['popreceipt' => $this->popReceipt];
    }
}
