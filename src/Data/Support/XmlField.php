<?php

namespace CodebarAg\MicrosoftAzure\Data\Support;

final class XmlField
{
    /**
     * Parse a flat list of same-named XML elements into arrays of their child values.
     *
     * @return list<array<string, mixed>>
     */
    public static function elements(string $xml, string $itemName): array
    {
        $previous = libxml_use_internal_errors(true);
        $document = simplexml_load_string($xml);
        libxml_use_internal_errors($previous);

        if ($document === false) {
            return [];
        }

        $matches = $document->xpath($itemName) ?: [];
        $items = [];

        foreach ($matches as $node) {
            $item = [];

            foreach ($node->children() as $child) {
                $item[$child->getName()] = (string) $child;
            }

            $items[] = $item;
        }

        return $items;
    }
}
