<?php

namespace ClaroBR\Tests\Unit\OperationAssistants;

use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Models\ClaroBandaLarga;
use ClaroBR\OperationAssistances\ClaroBandaLargaAssistant;
use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBandaLargaAssistantTest extends TestCase
{
    use SivFactoriesHelper, SivBindingHelper;


    /** @test */
    public function should_return_exception_when_not_exists_servico_id_in_operator_identifiers()
    {
        $service = $this->sivFactories()
            ->of(ClaroBandaLarga::class)
            ->make();

        $sivSaleAssistance = app()->make(ClaroBandaLargaAssistant::class);
        $this->expectException(ServiceNotIntegrated::class);
        $sivSaleAssistance->activate($service, []);
    }

    /** @test */
    public function should_activate_service_when_servico_id_exists_show_number_list()
    {
        $service           = $this->prepareUserAndSale();
        $sivSaleAssistance = app()->make(ClaroBandaLargaAssistant::class);

        $result = $sivSaleAssistance->activate($service, []);

        $this->assertContains('Por gentileza, escolha um telefone', $result->getContent());
    }

    /** @test */
    public function should_activate_service_when_msisdn_is_send_activate_with_success()
    {
        $service           = $this->prepareUserAndSale();
        $sivSaleAssistance = app()->make(ClaroBandaLargaAssistant::class);

        $result = $sivSaleAssistance->activate($service, ['msisdn' => ClaroBRTestBook::SUCCESS_MSISDN]);

        $this->assertContains('"status":"SUCESSO"', $result->getContent());
    }

    /** @test */
    public function should_activate_service_update_sale_to_accepted_when_response_status_is_success()
    {
        $service            = $this->prepareUserAndSale();
        $saleRepositoryMock = \Mockery::mock(SaleRepository::class)->makePartial();
        $saleRepositoryMock
            ->expects()
            ->updateService($service, ['status' => ServiceStatus::ACCEPTED])
            ->andReturn(new Service());
        $sivConnection     = resolve(SivConnectionInterface::class);
        $sivSaleAssistance = new ClaroBandaLargaAssistant($sivConnection, $saleRepositoryMock);

        $sivSaleAssistance->activate($service, ['msisdn' => ClaroBRTestBook::SUCCESS_MSISDN]);
    }

    /** @test */
    public function should_activate_service_update_msisdn_when_response_status_is_success()
    {
        $service            = $this->prepareUserAndSale();
        $saleRepositoryMock = \Mockery::mock(SaleRepository::class)->makePartial();
        $expectedMsisdn     = ['msisdn' => ClaroBRTestBook::SUCCESS_MSISDN];
        $saleRepositoryMock
            ->expects()
            ->updateService($service, $expectedMsisdn)
            ->andReturn(new Service());
        $sivConnection = resolve(SivConnectionInterface::class);

        $sivSaleAssistance = new ClaroBandaLargaAssistant($sivConnection, $saleRepositoryMock);

        $sivSaleAssistance->activate($service, $expectedMsisdn);
    }

    private function prepareUserAndSale(): Service
    {
        $this->bindSivResponse();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $serviceDerived = $this->sivFactories()
            ->of(ClaroBandaLarga::class)
            ->make();
        $service        = new Service($serviceDerived->toArray());

        $service->operatorIdentifiers = [
            'servico_id' => ClaroBRTestBook::SELECT_MSISDN_SERVICO_ID,
            'venda_id' => ClaroBRTestBook::SUCCESS_VENDA_ID
        ];

        return $service;
    }
}
