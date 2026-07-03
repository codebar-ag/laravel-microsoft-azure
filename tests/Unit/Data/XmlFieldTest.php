<?php

use CodebarAg\MicrosoftAzure\Data\Support\XmlField;

it('parses a list of xml elements into arrays', function (): void {
    $xml = '<QueueMessagesList>'
        .'<QueueMessage><MessageId>id-1</MessageId><PopReceipt>pop-1</PopReceipt><DequeueCount>1</DequeueCount></QueueMessage>'
        .'<QueueMessage><MessageId>id-2</MessageId><PopReceipt>pop-2</PopReceipt><DequeueCount>2</DequeueCount></QueueMessage>'
        .'</QueueMessagesList>';

    $items = XmlField::elements($xml, 'QueueMessage');

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe(['MessageId' => 'id-1', 'PopReceipt' => 'pop-1', 'DequeueCount' => '1'])
        ->and($items[1]['MessageId'])->toBe('id-2');
});

it('returns an empty array for malformed xml', function (): void {
    expect(XmlField::elements('<not-valid-xml', 'QueueMessage'))->toBe([]);
});

it('returns an empty array when no matching elements exist', function (): void {
    expect(XmlField::elements('<QueueMessagesList></QueueMessagesList>', 'QueueMessage'))->toBe([]);
});
