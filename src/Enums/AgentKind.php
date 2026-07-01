<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum AgentKind: string
{
    case Prompt = 'prompt';

    case Hosted = 'hosted';

    case ContainerApp = 'container_app';

    case Workflow = 'workflow';
}
