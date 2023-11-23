<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Services;

use TradeAppOne\Tests\TestCase;
use Illuminate\Database\Eloquent\Factory;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Assistances\SurfPernambucanasRechargeAssistance;
use SurfPernambucanas\Models\SurfPernambucanasPrePago;
use SurfPernambucanas\Tests\Traits\BindPagtelHttpClientMock;

class SurfPernambucanasRechargeAssistanceTest extends TestCase
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

    public function test_success_recharge_with_exists_card(): void
    {
        $service = $this->factory->of(SurfPernambucanasPrePago::class)->make();

        $assistance = resolve(SurfPernambucanasRechargeAssistance::class);

        $response = $assistance->integrateService($service, [
            "serviceTransaction" => "202106301424393467-0",
            "creditCard" => [
                "name"      => "Fulano de Tal",
                "cardNumber"=> "4539934475835500",
                "cvv"       => "135",
                "month"     => "10",
                "year"      => "25"
            ]
        ]);

        $this->assertInstanceOf(PagtelResponseAdapter::class, $response);
    }

    public function test_success_recharge_without_exists_card(): void
    {
        $service = $this->factory->of(SurfPernambucanasPrePago::class)->make();

        $assistance = resolve(SurfPernambucanasRechargeAssistance::class);

        $response = $assistance->integrateService($service, [
            "serviceTransaction"=> "202106301424393467-0",
            "creditCard"=> [
                "name"       => "Fulano de Tal",
                "cardNumber" => "4539934475835502",
                "cvv"        => "135",
                "month"      => "10",
                "year"       => "25"
            ]
        ]);

        $this->assertInstanceOf(PagtelResponseAdapter::class, $response);
    }

    /** @expectedException \TradeAppOne\Exceptions\BuildExceptions */
    public function test_failed_recharge_without_exists_card(): void
    {
        $service = $this->factory->of(SurfPernambucanasPrePago::class)->make();

        $assistance = resolve(SurfPernambucanasRechargeAssistance::class);

        $response = $assistance->integrateService($service, [
            "serviceTransaction"=> "202106301424393467-0",
            "creditCard"=> [
                "name"       => "Fulano de Tal",
                "cardNumber" => "5506775588250071",
                "cvv"        => "135",
                "month"      => "10",
                "year"       => "25"
            ]
        ]);

        $this->assertInstanceOf(PagtelResponseAdapter::class, $response);
    }
}
