<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\OpenAi\ModelListData;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ListModels;

final class OpenAiModelsResource extends OpenAiScopedResource
{
    public function list(): ModelListData
    {
        $response = $this->dispatchOpenAi(new ListModels);

        return ModelListData::fromAzure($this->jsonArray($response));
    }
}
