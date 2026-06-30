<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Support;

/**
 * Converts an absolute ARM URL (pagination `nextLink`, async-operation tracking
 * URL) into a path+query endpoint relative to the ARM connector base.
 *
 * Saloon disallows absolute URLs in resolveEndpoint(), and the ARM connector is
 * already based at https://management.azure.com with the bearer token attached.
 * We keep the raw query string verbatim so Azure's `$skiptoken` / `$filter` keys
 * survive (parse_str would mangle the leading `$`).
 */
final class AbsoluteArmUrl
{
    public static function toEndpoint(string $url): string
    {
        // Already relative — pass through.
        if (! preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $parts = parse_url($url);
        $endpoint = $parts['path'] ?? '/';

        if (isset($parts['query']) && $parts['query'] !== '') {
            $endpoint .= '?'.$parts['query'];
        }

        return $endpoint;
    }
}
