<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum ApimSubscriptionState: string
{
    case Submitted = 'submitted';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
