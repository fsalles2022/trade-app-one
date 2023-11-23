<?php

namespace ClaroBR\Tests\Unit;

use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Models\ControleBoleto;
use ClaroBR\Services\SivSaleAssistance;
use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\ControleBoletoHelper;
use TradeAppOne\Tests\TestCase;

class SivSaleAssistanceTest extends TestCase
{
    use ControleBoletoHelper, SivFactoriesHelper, SivBindingHelper;

    /** @test */
    public function should_return_ServiceNotIntegrated_when_try_activate_service_without_operators_identifiers()
    {
        $this->bindMountNewAttributesFromSiv();
        $this->bindSivResponse();

        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service           = $this->sivFactories()->of(ControleBoleto::class)->make()->toArray();
        $saleEntity        = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $sivSaleAssistance = app()->make(SivSaleAssistance::class);

        $this->expectException(ServiceNotIntegrated::class);

        $sivSaleAssistance->activate(
            $saleEntity->services()->first(),
            ['serviceTransaction' => ClaroBRTestBook::ERROR_SERVICE_TRANSACTION]
        );
    }

    /** @test */
    public function should_return_ServiceNotIntegrated_when_try_activate_service_without_SERVICO_ID_operators_identifiers()
    {
        $this->bindMountNewAttributesFromSiv();
        $this->bindSivResponse();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ControleBoleto::class)
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(SivSaleAssistance::class);

        $this->expectException(ServiceNotIntegrated::class);

        $sivSaleAssistance->activate(
            $saleEntity->services()->first(),
            ['serviceTransaction' => ClaroBRTestBook::ERROR_SERVICE_TRANSACTION]
        );
    }

    public function bindIncompleteRepositories(Sale $saleEntity)
    {
        $this->app->bind(SaleRepository::class, function () use ($saleEntity) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['findInSale', 'find', 'updateService'])
                ->getMock();

            $service                      = $saleEntity->services()->first();
            $service->operatorIdentifiers = [];

            $repository->method('find')->will($this->returnValue($saleEntity));
            $repository->method('findInSale')->will($this->returnValue($service));
            $repository->method('updateService')->will($this->returnValue($service));
            return $repository;
        });
    }

    /** @test */
    public function should_return_ServiceNotIntegrated_when_try_activate_service_with_operators_identifiers_invalid_filled()
    {
        $this->bindMountNewAttributesFromSiv();
        $this->bindSivResponse();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ControleBoleto::class)->states('activation_with_portability')
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);

        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(SivSaleAssistance::class);

        $saleEntity->services()->first()->operatorsIdentifiers = [];
        $this->expectException(ServiceNotIntegrated::class);

        $sivSaleAssistance->integrateService(
            $saleEntity->services()->first(),
            ['serviceTransaction' => ClaroBRTestBook::ERROR_SERVICE_TRANSACTION]
        );
    }

    /** @test */
    public function should_return_response_object_siv_response_when_try_activate_service_with_operators_identifiers()
    {
        $this->bindMountNewAttributesFromSiv();
        $this->bindSivResponse();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ControleBoleto::class)
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance            = app()->make(SivSaleAssistance::class);
        $service                      = $saleEntity->services()->first();
        $service->operatorIdentifiers = ['venda_id' => 123, 'servico_id' => 123];
        $sivReturn                    = $sivSaleAssistance->integrateService(
            $service,
            ['serviceTransaction' => ClaroBRTestBook::ERROR_SERVICE_TRANSACTION]
        );
        self::assertInstanceOf(JsonResponse::class, $sivReturn);
    }

    /** @test */
    public function should_return_siv_response_error_adapted_when_adapt_class_called()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service = $this->sivFactories()
            ->of(ControleBoleto::class)
            ->make()
            ->toArray();
        $this->bindSivResponse();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance            = app()->make(SivSaleAssistance::class);
        $service                      = $saleEntity->services()->first();
        $service->operatorIdentifiers = ['venda_id' => 123, 'servico_id' => 123];
        $sivReturn                    = $sivSaleAssistance->integrateService(
            $service,
            ['serviceTransaction' => ClaroBRTestBook::ERROR_SERVICE_TRANSACTION]
        );

        $sivReturn = json_decode($sivReturn->getContent(), true);

        self::assertArrayHasKey('errors', $sivReturn);
        self::assertArrayHasKey('message', $sivReturn['errors'][0]);
    }

    public function bindRepositories(Sale $saleEntity)
    {
        $this->app->bind(SaleRepository::class, function () use ($saleEntity) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['findInSale', 'find', 'updateService', 'pushLogService'])
                ->getMock();

            $service = $saleEntity->services()->first();

            $repository->method('find')->will($this->returnValue($saleEntity));
            $repository->method('findInSale')->will($this->returnValue($service));
            $repository->method('updateService')->will($this->returnValue($service));
            $repository->method('pushLogService')->will($this->returnValue($service));
            return $repository;
        });
    }

    /** @test */
    public function should_update_iccid_in_the_claro()
    {
        $this->bindSivResponse();

        Auth::setUser(UserBuilder::make()->build());

        $service = $this->sivFactories()
            ->of(ControleBoleto::class)
            ->make([
                'iccid' => '89550000000000000001',
                'invoiceType' => ServiceOption::CONTROLE_FACIL_LIO,
                'operatorIdentifiers' => [
                    'servico_id' => 123,
                    'venda_id' => 123
                ]
            ]);

        $service = SaleBuilder::make()
            ->withServices($service)
            ->build()
            ->services
            ->first();

        $default = '12345';

        $received = $this->service()->updateServiceSiv($service, [
            'iccid'       => $default,
            'invoiceType' => $default
        ]);

        $this->assertEquals($default, $received->iccid);
        $this->assertEquals($default, $received->invoiceType);
    }

    /** @test */
    public function should_return_exception_when_update_error()
    {
        $this->bindSivResponse();
        Auth::setUser(UserBuilder::make()->build());

        $service = $this->sivFactories()
            ->of(ControleBoleto::class)
            ->make([
                'invoiceType' => ServiceOption::CONTROLE_FACIL_LIO,
                'operatorIdentifiers' => [
                    'venda_id'   => 123,
                    'servico_id' => 123,
                ]
            ]);

        $this->expectExceptionCode(ClaroExceptions::UPDATE_ERROR);

        $this->service()->updateServiceSiv($service, [
            'iccid' => ClaroBRTestBook::ICCID_WITH_ERROR_UPDATE
        ]);
    }

    /** @test */
    public function should_not_update_when_key_has_same_value()
    {
        $service = $this->sivFactories()
            ->of(ControleBoleto::class)
            ->make([
                'invoiceType' => ServiceOption::CONTROLE_FACIL_LIO,
                'operatorIdentifiers' => [
                    'servico_id' => 123,
                    'venda_id' => 123
                ]
            ]);

        $this->service()->updateServiceSiv($service, [
            'invoiceType' => ServiceOption::CONTROLE_FACIL_LIO,
        ]);
    }

    private function service(): SivSaleAssistance
    {
        return resolve(SivSaleAssistance::class);
    }
}
