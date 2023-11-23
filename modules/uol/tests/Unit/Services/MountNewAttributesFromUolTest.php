<?php

namespace Uol\Tests\Unit\Services;

use Illuminate\Database\Eloquent\Factory;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;
use Uol\Models\UolCurso;
use Uol\Services\MountNewAttributesFromUol;

class MountNewAttributesFromUolTest extends TestCase
{
    use ArrayAssertTrait;
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = Factory::construct(\Faker\Factory::create(), base_path('modules/uol/Factories'));
    }

    /** @test */
    public function should_return_with_a_correct_structure_when_service_is_correct()
    {
        $mountNewAttributesFromUol = resolve(MountNewAttributesFromUol::class);
        $uolCurso                  = $this->factory->of(UolCurso::class)->make();
        $response                  = $mountNewAttributesFromUol->getAttributes($uolCurso->toArray());
        $this->assertArrayStructure($response, ['price', 'passportType', 'label']);
    }

    /** @test */
    public function should_return_product_not_found_exception_when_product_is_invalid()
    {
        $mountNewAttributesFromUol = resolve(MountNewAttributesFromUol::class);
        $uolCurso                  = $this->factory->of(UolCurso::class)->make(['product' => 'INVALID_PRODUCT']);
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage(trans('exceptions.third_party.default'));
        $mountNewAttributesFromUol->getAttributes($uolCurso->toArray());
    }
}
