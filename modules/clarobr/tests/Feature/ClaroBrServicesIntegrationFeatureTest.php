<?php


namespace ClaroBR\Tests\Feature;

use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\RemotePaymentException;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBrServicesIntegrationFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_422_when_integrator_response_has_not_payment_link(): void
    {
        $network = (new NetworkBuilder())->build();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();

        $serviceStructure               = ClaroServices::ControleFacil()->toArray();
        $serviceStructure['remoteSale'] = true;

        $service = factory(Service::class)->create($serviceStructure);
        $sale    = (new SaleBuilder())->withServices($service)->build();

        $payload = [
            'serviceTransaction' => $sale->services->first()->serviceTransaction,
            "invoiceType" =>  'CARTAO_CREDITO',
            "urlOrigin" => 'http://cea.localhost:8081'
        ];

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->put('sales', $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment(['message' => trans('siv::exceptions.' . ClaroExceptions::PAYMENT_URL_NOT_FOUND)]);
    }

    /** @test */
    public function should_return_422_when_has_not_url_origin(): void
    {
        $network = (new NetworkBuilder())->build();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();

        $serviceStructure               = ClaroServices::ControleFacil()->toArray();
        $serviceStructure['remoteSale'] = true;

        $service = factory(Service::class)->create($serviceStructure);
        $sale    = (new SaleBuilder())->withServices($service)->build();

        $payload = [
            'serviceTransaction' => $sale->services->first()->serviceTransaction,
            "invoiceType" =>  'CARTAO_CREDITO',
            "urlOrigin" => ''
        ];

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->put('sales', $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment(['message' => trans('exceptions.' . RemotePaymentException::PAYMENT_URL_NOT_CREATED)]);
    }

    /** @test */
    public function should_return_200_when_has_not_remote_sale_with_correct_response(): void
    {
        $network = (new NetworkBuilder())->build();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();

        $serviceStructure = ClaroServices::ControleFacil()->toArray();

        $service = factory(Service::class)->create($serviceStructure);
        $sale    = (new SaleBuilder())->withServices($service)->build();

        $payload = [
            'serviceTransaction' => $sale->services->first()->serviceTransaction,
            "invoiceType" =>  'CARTAO_CREDITO',
        ];

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->put('sales', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['type' => 'success']);
    }
}
