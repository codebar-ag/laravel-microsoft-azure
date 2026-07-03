<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use Saloon\Http\PendingRequest;
use Saloon\Repositories\Body\JsonBodyRepository;
use Saloon\Traits\Body\ChecksForHasBody;

/**
 * Drop-in replacement for Saloon's HasJsonBody. PHP's json_encode() has no
 * way to tell an empty "object" apart from an empty "list" —
 * `json_encode([])` always yields `[]`. Azure's ARM/REST validators reject
 * that for endpoints whose body schema is an object, even when every field
 * is optional. This forces `{}` instead of `[]` when the body is empty,
 * without affecting non-empty bodies (nested arrays are untouched).
 */
trait SendsJsonObjectBody
{
    use ChecksForHasBody;

    protected JsonBodyRepository $body;

    public function bootSendsJsonObjectBody(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Content-Type', 'application/json');
    }

    public function body(): JsonBodyRepository
    {
        $body = $this->body ??= new JsonBodyRepository($this->defaultBody());

        if ($body->all() === []) {
            $body->setJsonFlags(JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT);
        }

        return $body;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [];
    }
}
