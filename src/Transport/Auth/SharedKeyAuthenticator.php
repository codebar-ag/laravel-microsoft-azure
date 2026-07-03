<?php

namespace CodebarAg\MicrosoftAzure\Transport\Auth;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

/**
 * Azure Storage "Shared Key" (Full) request signing for Blob/Queue/File services.
 *
 * @see https://learn.microsoft.com/en-us/rest/api/storageservices/authorize-with-shared-key
 */
final class SharedKeyAuthenticator implements Authenticator
{
    public function __construct(
        private readonly string $accountName,
        private readonly string $accountKey,
    ) {}

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('x-ms-date', gmdate('D, d M Y H:i:s \G\M\T'));

        $rawBody = $pendingRequest->body()?->all();

        $signature = $this->sign(
            method: $pendingRequest->getMethod()->value,
            headers: $pendingRequest->headers()->all(),
            path: (string) parse_url($pendingRequest->getUrl(), PHP_URL_PATH),
            query: $pendingRequest->query()->all(),
            body: is_string($rawBody) ? $rawBody : '',
        );

        $pendingRequest->headers()->add('Authorization', "SharedKey {$this->accountName}:{$signature}");
    }

    /**
     * Pure signing function, isolated from PendingRequest so it can be exercised
     * with fixed inputs in a deterministic, known-answer unit test.
     *
     * @param  array<string, mixed>  $headers
     * @param  array<string, mixed>  $query
     */
    public function sign(string $method, array $headers, string $path, array $query, string $body): string
    {
        $stringToSign = $this->buildStringToSign($method, $headers, $path, $query, $body);

        return base64_encode(hash_hmac(
            'sha256',
            $stringToSign,
            base64_decode($this->accountKey),
            true,
        ));
    }

    /**
     * @param  array<string, mixed>  $headers
     * @param  array<string, mixed>  $query
     */
    private function buildStringToSign(string $method, array $headers, string $path, array $query, string $body): string
    {
        $contentType = is_string($headers['Content-Type'] ?? null) ? $headers['Content-Type'] : '';
        $contentLength = $body !== '' ? (string) strlen($body) : '';

        $lines = [
            $method,
            '', // Content-Encoding
            '', // Content-Language
            $contentLength,
            '', // Content-MD5
            $contentType,
            '', // Date (empty — x-ms-date is used instead)
            '', // If-Modified-Since
            '', // If-Match
            '', // If-None-Match
            '', // If-Unmodified-Since
            '', // Range
        ];

        return implode("\n", $lines)
            ."\n".$this->canonicalizedHeaders($headers)
            .$this->canonicalizedResource($path, $query);
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    private function canonicalizedHeaders(array $headers): string
    {
        $msHeaders = [];

        foreach ($headers as $name => $value) {
            $lower = strtolower($name);

            if (str_starts_with($lower, 'x-ms-') && is_string($value)) {
                $msHeaders[$lower] = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
            }
        }

        ksort($msHeaders);

        $lines = '';

        foreach ($msHeaders as $name => $value) {
            $lines .= "{$name}:{$value}\n";
        }

        return $lines;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function canonicalizedResource(string $path, array $query): string
    {
        $resource = '/'.$this->accountName.($path !== '' ? $path : '/');

        if ($query === []) {
            return $resource;
        }

        $normalized = [];

        foreach ($query as $name => $value) {
            $normalized[strtolower((string) $name)][] = is_scalar($value) ? (string) $value : '';
        }

        ksort($normalized);

        foreach ($normalized as $name => $values) {
            sort($values);
            $resource .= "\n{$name}:".implode(',', $values);
        }

        return $resource;
    }
}
