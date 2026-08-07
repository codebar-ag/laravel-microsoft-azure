<?php

namespace CodebarAg\MicrosoftAzure\Enums;

use CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload;

/**
 * How finely a {@see CostQueryPayload} slices its time period.
 *
 * Cost Management accepts only these values on `dataset.granularity`; anything
 * else is a 400. `Monthly` is absent on purpose — the API does not offer it for
 * the query endpoint, and callers wanting monthly totals ask for a one-month
 * period at {@see None}.
 *
 * **{@see Daily} adds a column.** The response gains `UsageDate` (an integer,
 * `yyyyMMdd`, not a string date), and the row count multiplies by the number of
 * days in the period. Callers that assumed one row per grouping have to handle
 * that — which is why this defaults to {@see None} and daily is opt-in.
 */
enum CostGranularity: string
{
    /** One row per grouping combination for the whole period. */
    case None = 'None';

    /** One row per grouping combination per day, plus a `UsageDate` column. */
    case Daily = 'Daily';
}
