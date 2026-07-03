<?php

namespace CodebarAg\MicrosoftAzure\Data\LogAnalytics;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class QueryTableData extends AzureData
{
    /**
     * @param  list<string>  $columns
     * @param  list<list<mixed>>  $rows
     */
    public function __construct(
        public string $name,
        public array $columns = [],
        public array $rows = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $columns = [];
        $rawColumns = $data['columns'] ?? [];

        if (is_array($rawColumns)) {
            foreach ($rawColumns as $column) {
                if (is_array($column)) {
                    $columns[] = Field::optionalString(Field::stringKeyArray($column), 'name');
                }
            }
        }

        $rows = [];
        $rawRows = $data['rows'] ?? [];

        if (is_array($rawRows)) {
            foreach ($rawRows as $row) {
                if (is_array($row)) {
                    $rows[] = array_values($row);
                }
            }
        }

        return new self(
            name: Field::optionalString($data, 'name'),
            columns: $columns,
            rows: $rows,
        );
    }

    /**
     * Rows as associative arrays keyed by column name.
     *
     * @return list<array<string, mixed>>
     */
    public function rowsAssoc(): array
    {
        $assoc = [];

        foreach ($this->rows as $row) {
            $combined = [];

            foreach ($this->columns as $index => $column) {
                $combined[$column] = $row[$index] ?? null;
            }

            $assoc[] = $combined;
        }

        return $assoc;
    }
}
