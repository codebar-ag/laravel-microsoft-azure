<?php

namespace CodebarAg\MicrosoftAzure\Requests\StorageQueue;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasStringBody;

final class SendMessage extends Request implements HasBody
{
    use HasStringBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $queueName,
        public readonly string $encodedBody,
        public readonly ?int $visibilityTimeoutSeconds = null,
        public readonly ?int $messageTtlSeconds = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.$this->queueName.'/messages';
    }

    /** @return array<string, int> */
    protected function defaultQuery(): array
    {
        return array_filter([
            'visibilitytimeout' => $this->visibilityTimeoutSeconds,
            'messagettl' => $this->messageTtlSeconds,
        ], fn (?int $value) => $value !== null);
    }

    protected function defaultBody(): string
    {
        return '<QueueMessage><MessageText>'
            .htmlspecialchars($this->encodedBody, ENT_XML1)
            .'</MessageText></QueueMessage>';
    }

    /** @return array<string, string> */
    protected function defaultHeaders(): array
    {
        return ['Content-Type' => 'application/xml'];
    }
}
