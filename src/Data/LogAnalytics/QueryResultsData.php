<?php

namespace CodebarAg\MicrosoftAzure\Data\LogAnalytics;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class QueryResultsData extends AzureData
{
    /**
     * @param  list<QueryTableData>  $tables
     */
    public function __construct(
        public array $tables = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $tables = [];
        $rawTables = $data['tables'] ?? [];

        if (is_array($rawTables)) {
            foreach ($rawTables as $table) {
                if (is_array($table)) {
                    $tables[] = QueryTableData::fromAzure(Field::stringKeyArray($table));
                }
            }
        }

        return new self(tables: $tables);
    }

    public function table(string $name = 'PrimaryResult'): ?QueryTableData
    {
        foreach ($this->tables as $table) {
            if ($table->name === $name) {
                return $table;
            }
        }

        return null;
    }

    /**
     * Rows of the given table as associative arrays keyed by column name.
     *
     * @return list<array<string, mixed>>
     */
    public function rowsAssoc(string $table = 'PrimaryResult'): array
    {
        return $this->table($table)?->rowsAssoc() ?? [];
    }
}
