<?php


namespace Outsourced\GPA\tests;

use Outsourced\Enums\Outsourced;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class GPATestHelpers
{
    public static function createObject(): \stdClass
    {
        $object          = new \stdClass();
        $object->network = (new NetworkBuilder())->withSlug(Outsourced::GPA)->build();

        $object->pointOfSale = (new PointOfSaleBuilder())
            ->withNetwork($object->network)
            ->withState('with_identifiers')
            ->build();

        $object->user = (new UserBuilder())
            ->withPointOfSale($object->pointOfSale)
            ->withNetwork($object->network)
            ->build();

        return $object;
    }

    public static function service(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/service_strucure.json'), true);
    }
}
