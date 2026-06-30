<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class CostQueryResultData extends AzureData
{
    /**
     * @param  list<string>  $columns
     * @param  list<array<string, mixed>>  $rows
     */
    public function __construct(
        public array $columns,
        public array $rows,
        public ?string $currency = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = Field::properties($data);

        $rawColumns = $properties['columns'] ?? [];
        $rawRows = $properties['rows'] ?? [];

        /** @var list<string> $columns */
        $columns = [];

        if (is_array($rawColumns)) {
            foreach ($rawColumns as $column) {
                if (is_array($column) && isset($column['name']) && is_string($column['name'])) {
                    $columns[] = $column['name'];
                }
            }
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = [];

        if (is_array($rawRows)) {
            foreach ($rawRows as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $values = array_values($row);
                $mapped = [];

                foreach ($columns as $index => $name) {
                    $mapped[$name] = $values[$index] ?? null;
                }

                $rows[] = $mapped;
            }
        }

        $currency = null;

        foreach (['Currency', 'BillingCurrency'] as $currencyColumn) {
            if (in_array($currencyColumn, $columns, true) && isset($rows[0][$currencyColumn]) && is_string($rows[0][$currencyColumn])) {
                $currency = $rows[0][$currencyColumn];

                break;
            }
        }

        return new self(
            columns: $columns,
            rows: $rows,
            currency: $currency,
        );
    }
}
