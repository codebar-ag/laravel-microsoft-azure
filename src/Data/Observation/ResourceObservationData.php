<?php

namespace CodebarAg\MicrosoftAzure\Data\Observation;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ObservedState;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;

/**
 * The result of an actual-state read: what Azure says is there right now.
 *
 * Unlike the `get()` gateways — which throw {@see NotFoundException}
 * or {@see ForbiddenException} — an
 * observation is total: absence and inaccessibility are answers, not errors.
 * Only genuinely unexpected failures (400, 429, 5xx) still throw.
 *
 * `$provisioningState` is null both when Azure reported no `provisioningState`
 * at all and when it reported one this package does not have a case for; the
 * verbatim string is always kept in `$reportedProvisioningState` so callers can
 * tell those apart and so an unrecognised value is visible rather than silently
 * flattened. An unrecognised value is deliberately mapped to
 * {@see ObservedState::Provisioning} — never to {@see ObservedState::Available}.
 */
final class ResourceObservationData extends AzureData
{
    public function __construct(
        public ObservedState $state,
        public ?string $id = null,
        public ?string $name = null,
        public ?string $location = null,
        public ?string $reportedProvisioningState = null,
        public ?ProvisioningState $provisioningState = null,
        public ?AzureData $resource = null,
        public ?int $statusCode = null,
        public ?string $message = null,
        public ?string $scheduledPurgeDate = null,
    ) {}

    /**
     * Azure returned a body. Derive the state from the raw
     * `properties.provisioningState`, not from the typed DTO, so providers whose
     * DTO drops or re-types that field are still read correctly.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data, ?AzureData $resource = null, int $statusCode = 200): self
    {
        $reported = Field::arrNullableString($data, 'properties.provisioningState');
        $provisioningState = $reported !== null ? ProvisioningState::tryFrom($reported) : null;

        return new self(
            state: self::stateFor($reported, $provisioningState),
            id: Field::nullableString($data, 'id'),
            name: Field::nullableString($data, 'name'),
            location: Field::nullableString($data, 'location'),
            reportedProvisioningState: $reported,
            provisioningState: $provisioningState,
            resource: $resource,
            statusCode: $statusCode,
        );
    }

    /**
     * Azure returned 404 and nothing holds the name.
     */
    public static function absent(?int $statusCode = null, ?string $message = null): self
    {
        return new self(
            state: ObservedState::Absent,
            statusCode: $statusCode,
            message: $message,
        );
    }

    /**
     * Azure returned 403. Whether the resource exists is unknown — say so
     * rather than guessing, because the two plausible guesses ("absent", so
     * create it; "available", so use it) are both wrong and both destructive.
     */
    public static function inaccessible(?int $statusCode = null, ?string $message = null): self
    {
        return new self(
            state: ObservedState::Inaccessible,
            statusCode: $statusCode,
            message: $message,
        );
    }

    /**
     * The live resource is gone but a deleted-not-purged one still holds the name.
     */
    public static function softDeleted(
        ?string $id = null,
        ?string $name = null,
        ?string $location = null,
        ?string $scheduledPurgeDate = null,
        ?AzureData $resource = null,
    ): self {
        return new self(
            state: ObservedState::SoftDeleted,
            id: $id,
            name: $name,
            location: $location,
            resource: $resource,
            scheduledPurgeDate: $scheduledPurgeDate,
        );
    }

    private static function stateFor(?string $reported, ?ProvisioningState $provisioningState): ObservedState
    {
        // Providers that expose no provisioningState at all (Microsoft Graph,
        // Key Vault's data plane): a body came back, so it is there and readable.
        if ($reported === null) {
            return ObservedState::Available;
        }

        // A value this package has no case for. Assume in-flight; never claim usable.
        if ($provisioningState === null) {
            return ObservedState::Provisioning;
        }

        return match ($provisioningState) {
            ProvisioningState::Succeeded => ObservedState::Available,
            ProvisioningState::Failed, ProvisioningState::Canceled => ObservedState::Failed,
            ProvisioningState::Deleting => ObservedState::Deleting,
            default => ObservedState::Provisioning,
        };
    }
}
