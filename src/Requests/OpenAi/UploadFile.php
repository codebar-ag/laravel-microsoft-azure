<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use RuntimeException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasMultipartBody;

final class UploadFile extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $filePath,
        public readonly string $purpose,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/files';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::OPENAI];
    }

    /**
     * @return array<int, MultipartValue>
     */
    protected function defaultBody(): array
    {
        if (! is_readable($this->filePath)) {
            throw new RuntimeException("File [{$this->filePath}] is not readable.");
        }

        return [
            new MultipartValue(name: 'purpose', value: $this->purpose),
            new MultipartValue(
                name: 'file',
                value: fopen($this->filePath, 'r') ?: throw new RuntimeException("File [{$this->filePath}] could not be opened."),
                filename: basename($this->filePath),
            ),
        ];
    }
}
