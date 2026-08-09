<?php

namespace CodebarAg\MicrosoftAzure\Enums;

/**
 * `properties.provisioningState`, verbatim.
 *
 * Every case here is emitted by at least one resource provider this package
 * covers. A value missing from this enum deserializes to `null`, which reads as
 * "the provider said nothing" — so an incomplete enum makes a resource that is
 * still mid-flight look like one with no lifecycle at all. `ResolvingDNS`
 * (Storage, Cognitive Services), `RegisteringDns` (Key Vault) and `Moving`
 * (Cognitive Services) were exactly that gap.
 */
enum ProvisioningState: string
{
    case Succeeded = 'Succeeded';
    case Failed = 'Failed';
    case Canceled = 'Canceled';
    case Accepted = 'Accepted';
    case Running = 'Running';
    case Creating = 'Creating';
    case Updating = 'Updating';
    case Deleting = 'Deleting';
    case Moving = 'Moving';
    case ResolvingDNS = 'ResolvingDNS';
    case RegisteringDns = 'RegisteringDns';
    case NotSpecified = 'NotSpecified';

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Succeeded,
            self::Failed,
            self::Canceled,
        ], true);
    }
}
