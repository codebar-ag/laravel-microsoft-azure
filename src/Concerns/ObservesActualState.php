<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;
use CodebarAg\MicrosoftAzure\Resources\Resource;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * Turns a throwing `get()` into a total actual-state read.
 *
 * The `get()` gateways are right to throw: a caller that asked for a resource
 * by name and got a 404 has a bug. A caller reconciling desired state against
 * actual state has the opposite need — 404 and 403 are the interesting answers,
 * and wrapping every read in try/catch at the call site is how "deleted in the
 * portal" ends up invisible. Everything else (400, 429, 5xx) still throws,
 * because none of those describe the resource.
 *
 * Mixed into {@see \CodebarAg\MicrosoftAzure\Resources\Resource} subclasses, so
 * `$this->sendArm()`, `$this->sendGraph()`, `$this->sendKeyVault()` and
 * `$this->jsonArray()` are available.
 */
trait ObservesActualState
{
    /**
     * @param  (callable(array<string, mixed>): AzureData)|null  $map  builds the typed DTO from the response body
     * @param  (callable(): (ResourceObservationData|null))|null  $whenMissing  runs on 404; return an
     *                                                                          observation to override `Absent` (a soft-delete lookup), or null to accept it
     */
    protected function observeArm(
        Request $request,
        ?callable $map = null,
        ?callable $whenMissing = null,
    ): ResourceObservationData {
        return $this->observeUsing(fn (): Response => $this->sendArm($request), $map, $whenMissing);
    }

    /**
     * @param  (callable(array<string, mixed>): AzureData)|null  $map
     */
    protected function observeGraph(Request $request, ?callable $map = null): ResourceObservationData
    {
        return $this->observeUsing(fn (): Response => $this->sendGraph($request), $map);
    }

    /**
     * @param  (callable(array<string, mixed>): AzureData)|null  $map
     */
    protected function observeKeyVault(
        Request $request,
        string $vaultHost,
        ?callable $map = null,
    ): ResourceObservationData {
        return $this->observeUsing(fn (): Response => $this->sendKeyVault($request, $vaultHost), $map);
    }

    /**
     * @param  callable(): Response  $send
     * @param  (callable(array<string, mixed>): AzureData)|null  $map
     * @param  (callable(): (ResourceObservationData|null))|null  $whenMissing
     */
    private function observeUsing(
        callable $send,
        ?callable $map = null,
        ?callable $whenMissing = null,
    ): ResourceObservationData {
        try {
            $response = $send();
        } catch (NotFoundException $exception) {
            $override = $whenMissing !== null ? $whenMissing() : null;

            return $override ?? ResourceObservationData::absent($exception->statusCode, $exception->azureMessage);
        } catch (ForbiddenException $exception) {
            return ResourceObservationData::inaccessible($exception->statusCode, $exception->azureMessage);
        }

        $json = $this->jsonArray($response);

        return ResourceObservationData::fromAzure($json, $map !== null ? $map($json) : null, $response->status());
    }
}
