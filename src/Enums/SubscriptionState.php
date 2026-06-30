<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum SubscriptionState: string
{
    case Enabled = 'Enabled';
    case Warned = 'Warned';
    case PastDue = 'PastDue';
    case Disabled = 'Disabled';
    case Deleted = 'Deleted';
}
