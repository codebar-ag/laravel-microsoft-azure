<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Requests\OpenAi\DeleteFile;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ListFiles;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\UploadFile;

final class OpenAiFilesResource extends OpenAiScopedResource
{
    /** @return array<string, mixed> */
    public function list(): array
    {
        $response = $this->dispatchOpenAi(new ListFiles);

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function upload(string $filePath, string $purpose): array
    {
        $response = $this->dispatchOpenAi(new UploadFile(
            $filePath,
            $purpose,
        ));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function delete(string $fileId): array
    {
        $response = $this->dispatchOpenAi(new DeleteFile($fileId));

        return $this->jsonArray($response);
    }
}
