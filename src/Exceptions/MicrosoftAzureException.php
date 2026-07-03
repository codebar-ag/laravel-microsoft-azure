<?php

namespace CodebarAg\MicrosoftAzure\Exceptions;

use RuntimeException;
use Throwable;

class MicrosoftAzureException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $connection = null,
        public readonly ?string $azureMessage = null,
        public readonly ?string $requestId = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'connection' => $this->connection,
            'status' => $this->statusCode,
            'azure_message' => $this->azureMessage,
            'request_id' => $this->requestId,
        ];
    }
}
