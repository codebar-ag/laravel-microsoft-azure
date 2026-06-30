<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Kudu\KuduDeploymentData;
use CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus;
use CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy;

final class AppServiceResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $appName,
    ) {
        parent::__construct($client);
    }

    public function zipDeploy(string $zipFilePath): KuduDeploymentData
    {
        $response = $this->sendKudu(new ZipDeploy($zipFilePath), $this->appName);

        return KuduDeploymentData::fromAzure($this->jsonArray($response));
    }

    public function deploymentStatus(string $deploymentId): KuduDeploymentData
    {
        $response = $this->sendKudu(new GetDeploymentStatus($deploymentId), $this->appName);

        return KuduDeploymentData::fromAzure($this->jsonArray($response));
    }
}
