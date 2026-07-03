<?php

namespace CodebarAg\MicrosoftAzure\Requests\Kudu;

use RuntimeException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasMultipartBody;

final class ZipDeploy extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $zipFilePath,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/zipdeploy';
    }

    /**
     * @return array<int, MultipartValue>
     */
    protected function defaultBody(): array
    {
        if (! is_readable($this->zipFilePath)) {
            throw new RuntimeException("Zip file [{$this->zipFilePath}] is not readable.");
        }

        $handle = $this->openReadableStream($this->zipFilePath);

        return [
            new MultipartValue(
                name: 'package',
                value: $handle,
                filename: basename($this->zipFilePath),
            ),
        ];
    }

    /**
     * @return resource
     */
    protected function openReadableStream(string $path)
    {
        $handle = @fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("Zip file [{$path}] could not be opened.");
        }

        return $handle;
    }
}
