<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class McpJsonRpcPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $params
     */
    public function __construct(
        public readonly string $method,
        public readonly array $params = [],
        public readonly int|string $id = 1,
    ) {}

    public function toAzureBody(): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $this->id,
            'method' => $this->method,
            'params' => $this->params,
        ];
    }
}
