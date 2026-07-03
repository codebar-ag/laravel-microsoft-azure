<?php

namespace CodebarAg\MicrosoftAzure\Enums;

enum FoundryFeature: string
{
    case ContainerAgents = 'ContainerAgents=V1Preview';

    case HostedAgents = 'HostedAgents=V1Preview';

    case WorkflowAgents = 'WorkflowAgents=V1Preview';

    case AgentEndpoints = 'AgentEndpoints=V1Preview';

    /**
     * NOTE: dotted-namespace style, unlike the flat `Xyz=V1Preview` cases above.
     * Sourced from a third-party doc (Stand: Juli 2026, Public Preview) —
     * unverified against a live tenant. If wrong, only this literal needs to
     * change; toHeader() emits it verbatim.
     */
    case CodeAgents = 'Agents.CodeAgents=V1Preview';

    case ExternalAgents = 'ExternalAgents=V1Preview';

    case Toolboxes = 'Toolboxes=V1Preview';

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
