<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Support;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * GETs an absolute Azure `nextLink` / `@odata.nextLink` pagination URL.
 *
 * The URL already carries its api-version and skip-token query; we send its
 * path+query relative to the ARM connector base (Saloon disallows absolute
 * endpoints), preserving the raw query so Azure's `$skiptoken` survives.
 */
final class GetNextPage extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $url,
    ) {}

    public function resolveEndpoint(): string
    {
        // Azure pagination links are absolute, but Saloon disallows absolute
        // endpoints and the ARM connector base is https://management.azure.com.
        // Strip scheme+host and send the path (+ raw query, preserving Azure's
        // $skiptoken/$filter keys verbatim) relative to that base, keeping auth.
        return AbsoluteArmUrl::toEndpoint($this->url);
    }
}
