<?php

namespace CodebarAg\MicrosoftAzure\Enums;

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
