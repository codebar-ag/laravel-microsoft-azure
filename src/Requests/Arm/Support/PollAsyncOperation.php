<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Support;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Polls an Azure async-operation tracking URL.
 *
 * Azure long-running operations return an absolute `Azure-AsyncOperation` or
 * `Location` URL (already carrying its own api-version). We GET its path+query
 * relative to the ARM connector base (Saloon disallows absolute endpoints).
 */
final class PollAsyncOperation extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $url,
    ) {}

    public function resolveEndpoint(): string
    {
        // Tracking URLs are absolute; Saloon disallows absolute endpoints and the
        // ARM connector base is https://management.azure.com (bearer token attached).
        // Send the path + raw query relative to that base.
        return AbsoluteArmUrl::toEndpoint($this->url);
    }
}
