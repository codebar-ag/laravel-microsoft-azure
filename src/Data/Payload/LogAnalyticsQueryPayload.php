<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class LogAnalyticsQueryPayload extends AzurePayload
{
    public function __construct(
        public readonly string $query,
        public readonly ?string $timespan = null,
    ) {}

    public function toAzureBody(): array
    {
        $body = ['query' => $this->query];

        if ($this->timespan !== null) {
            $body['timespan'] = $this->timespan;
        }

        return $body;
    }
}
