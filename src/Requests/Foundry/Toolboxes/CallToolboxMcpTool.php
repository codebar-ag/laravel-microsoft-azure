<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Data\Payload\McpJsonRpcPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class CallToolboxMcpTool extends FoundryAgentsRequest implements FoundryFeatureRequest, HasBody
{
    use HasFoundryFeatures;
    use SendsJsonObjectBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function __construct(
        public readonly string $toolboxName,
        public readonly string $version,
        public readonly string $toolName,
        public readonly array $arguments = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/toolboxes/'.$this->toolboxName.'/versions/'.$this->version.'/mcp';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return (new McpJsonRpcPayload('tools/call', ['name' => $this->toolName, 'arguments' => $this->arguments]))->toAzureBody();
    }
}
