<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use Illuminate\Support\Arr;

final class UsageDetailData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?float $cost = null,
        public ?string $currency = null,
        public ?string $date = null,
        public ?string $product = null,
        public ?string $meterName = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $cost = Arr::get($data, 'properties.cost', Arr::get($data, 'properties.costInBillingCurrency'));

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            cost: is_numeric($cost) ? (float) $cost : null,
            currency: Field::arrNullableString($data, 'properties.billingCurrency')
                ?? Field::arrNullableString($data, 'properties.currency'),
            date: Field::arrNullableString($data, 'properties.date'),
            product: Field::arrNullableString($data, 'properties.product'),
            meterName: Field::arrNullableString($data, 'properties.meterDetails.meterName'),
        );
    }
}
