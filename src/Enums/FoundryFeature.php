<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum FoundryFeature: string
{
    case ContainerAgents = 'ContainerAgents=V1Preview';

    case HostedAgents = 'HostedAgents=V1Preview';

    case WorkflowAgents = 'WorkflowAgents=V1Preview';

    case AgentEndpoints = 'AgentEndpoints=V1Preview';

    /**
     * @param  list<self>  $features
     */
    public static function toHeader(array $features): string
    {
        return implode(',', array_map(
            static fn (self $feature): string => $feature->value,
            $features,
        ));
    }
}
