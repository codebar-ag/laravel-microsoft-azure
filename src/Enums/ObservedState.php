<?php

namespace CodebarAg\MicrosoftAzure\Enums;

/**
 * What an actual-state read found, as opposed to what Azure reports about a
 * resource it already handed us ({@see ProvisioningState}).
 *
 * `ProvisioningState` mirrors `properties.provisioningState` verbatim and only
 * exists once ARM has returned a body. This enum answers the two questions a
 * caller reconciling desired against actual state actually asks — *is it
 * there* and *can it be used* — and it can answer them when ARM returned no
 * body at all (404 / 403), which is precisely when the answer matters.
 *
 * The three states that are easy to collapse and must not be:
 *
 *  - {@see self::Provisioning} vs {@see self::Available} — a resource exists
 *    long before it works. Treating "exists" as "usable" makes a caller
 *    declare success over something nothing can talk to yet.
 *  - {@see self::Inaccessible} vs {@see self::Absent} — ARM answers 403, not
 *    404, when the calling principal has no read permission, and it keeps
 *    doing so for tens of seconds after a role assignment is granted. 403 says
 *    nothing about existence; reporting it as absent invites a duplicate create.
 *  - {@see self::SoftDeleted} vs {@see self::Absent} — a deleted Key Vault holds
 *    its name for its retention window, and a deleted Cognitive Services /
 *    Foundry account holds its name *and* its subscription-wide model quota.
 *    ARM 409s any create against a held name and no amount of waiting frees it,
 *    so a caller told "absent" will retry the create forever.
 */
enum ObservedState: string
{
    /** ARM/Graph returned 404, and no soft-deleted corpse holds the name. */
    case Absent = 'absent';

    /** Present, but `provisioningState` is not terminal — or is a value this package does not recognise. */
    case Provisioning = 'provisioning';

    /** Present, provisioned, and readable by the calling principal. */
    case Available = 'available';

    /** Present and terminally broken (`Failed` / `Canceled`); it will not become available on its own. */
    case Failed = 'failed';

    /** Present but on its way out; it still holds its name until the delete completes. */
    case Deleting = 'deleting';

    /** Deleted but not purged: the name (and, for Foundry accounts, the model quota) is still held. */
    case SoftDeleted = 'soft_deleted';

    /** ARM/Graph answered 403. Existence is unknown — the calling principal cannot read it (yet). */
    case Inaccessible = 'inaccessible';

    /**
     * Safe to hand to callers as a working resource.
     */
    public function isUsable(): bool
    {
        return $this === self::Available;
    }

    /**
     * A live resource exists. Excludes {@see self::SoftDeleted} (a corpse, not a
     * resource) and {@see self::Inaccessible} (existence unknown).
     */
    public function isPresent(): bool
    {
        return in_array($this, [
            self::Provisioning,
            self::Available,
            self::Failed,
            self::Deleting,
        ], true);
    }

    /**
     * A create against this name will fail (ARM 409) until something else
     * happens first — a purge, or the in-flight delete completing.
     */
    public function blocksCreate(): bool
    {
        return in_array($this, [
            self::SoftDeleted,
            self::Deleting,
        ], true);
    }

    /**
     * Nothing will change without an action from the caller — re-reading is
     * pointless. The inverse is the "keep polling" set: `Provisioning`,
     * `Deleting`, and `Inaccessible` (which resolves itself once an RBAC grant
     * propagates).
     */
    public function isSettled(): bool
    {
        return in_array($this, [
            self::Absent,
            self::Available,
            self::Failed,
            self::SoftDeleted,
        ], true);
    }
}
