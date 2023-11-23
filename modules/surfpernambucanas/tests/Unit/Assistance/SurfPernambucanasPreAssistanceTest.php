<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Services;

use TradeAppOne\Tests\TestCase;
use Illuminate\Database\Eloquent\Factory;
use SurfPernambucanas\Adapters\PagtelActivationActivateResponseAdapter;
use SurfPernambucanas\Assistances\SurfPernambucanasPreAssistance;
use SurfPernambucanas\Models\SurfPernambucanasPrePago;
use SurfPernambucanas\Tests\Traits\BindPagtelHttpClientMock;

class SurfPernambucanasPreAssistanceTest extends TestCase
{
    use BindPagtelHttpClientMock;
    
    /** @var Factory */
    protected $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = Factory::construct(\Faker\Factory::create(), base_path('modules/surfpernambucanas/tests/Factories'));
        
        $this->bindPagtelHttpClient();
    }

    public function test_success_activate(): void
    {
        $service = $this->factory->of(SurfPernambucanasPrePago::class)->make();

        $assistance = resolve(SurfPernambucanasPreAssistance::class);

        $response = $assistance->integrateService($service, [
            "serviceTransaction" => "202106301424393467-0",
            "creditCard" => [
                "name"      => "Fulano de Tal",
                "cardNumber"=> "4539934475835500",
                "cvv"       => "135",
                "month"     => "10",
                "year"      => "25",
                "program"   => false
            ]
        ]);

        $this->assertInstanceOf(PagtelActivationActivateResponseAdapter::class, $response);
    }
}
