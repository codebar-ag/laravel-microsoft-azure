<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Exceptions\AuthenticationException;
use CodebarAg\MicrosoftAzure\Exceptions\BadRequestException;
use CodebarAg\MicrosoftAzure\Exceptions\ConflictException;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Exceptions\MicrosoftAzureException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;
use CodebarAg\MicrosoftAzure\Exceptions\RateLimitException;
use CodebarAg\MicrosoftAzure\Exceptions\RequestException;
use CodebarAg\MicrosoftAzure\Security\Redactor;
use Illuminate\Support\Str;
use Saloon\Http\Response;

final class ResponseValidator
{
    public static function validate(Response $response, ?string $connection = null): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $message = self::extractMessage($response);
        $requestId = self::requestId($response);
        $summary = "Azure request to connection [{$connection}] failed with HTTP {$status}"
            .($message !== null ? ": {$message}" : '.');

        throw match ($status) {
            400 => new BadRequestException($summary, $status, $connection, $message, $requestId),
            401 => new AuthenticationException($summary, $status, $connection, $message, $requestId),
            403 => new ForbiddenException($summary, $status, $connection, $message, $requestId),
            404 => new NotFoundException($summary, $status, $connection, $message, $requestId),
            409 => new ConflictException($summary, $status, $connection, $message, $requestId),
            429 => new RateLimitException($summary, $status, $connection, $message, $requestId),
            default => $status >= 400 && $status < 500
                ? new RequestException($summary, $status, $connection, $message, $requestId)
                : new MicrosoftAzureException($summary, $status, $connection, $message, $requestId),
        };
    }

    private static function extractMessage(Response $response): ?string
    {
        $body = (string) $response->body();
        if ($body === '') {
            return null;
        }

        $redactor = new Redactor;

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            foreach (['message', 'error_description', 'error', 'Message'] as $key) {
                $value = $decoded[$key] ?? null;
                if (is_string($value) && $value !== '') {
                    return $redactor->string($value);
                }

                if (is_array($value) && isset($value['message']) && is_string($value['message'])) {
                    return $redactor->string($value['message']);
                }
            }

            return null;
        }

        $summary = trim((string) preg_replace('/\s\s+/', ' ', strip_tags($body)));

        return $summary !== '' ? $redactor->string(Str::limit($summary, 300)) : null;
    }

    private static function requestId(Response $response): ?string
    {
        foreach (['x-ms-request-id', 'X-Request-Id', 'Request-Id'] as $header) {
            $value = $response->header($header);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
