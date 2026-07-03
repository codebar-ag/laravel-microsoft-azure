<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum DeploymentMode: string
{
    case Incremental = 'Incremental';
    case Complete = 'Complete';
}
