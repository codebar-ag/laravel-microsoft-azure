<?php

namespace CodebarAg\MicrosoftAzure\Requests\StorageQueue;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ReceiveMessages extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $queueName,
        public readonly int $numberOfMessages = 1,
        public readonly ?int $visibilityTimeoutSeconds = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.$this->queueName.'/messages';
    }

    /** @return array<string, int> */
    protected function defaultQuery(): array
    {
        return array_filter([
            'numofmessages' => $this->numberOfMessages,
            'visibilitytimeout' => $this->visibilityTimeoutSeconds,
        ], fn (?int $value) => $value !== null);
    }
}
