<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum SubscriptionWorkload: string
{
    case Production = 'Production';
    case DevTest = 'DevTest';
}
