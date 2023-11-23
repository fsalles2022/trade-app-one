<?php

namespace TradeAppOne\Tests\Unit\Domain\Factories;

use ClaroBR\Models\ClaroBandaLarga;
use ClaroBR\Models\ClaroPos;
use ClaroBR\Models\ClaroPre;
use ClaroBR\Services\MountNewAttributeFromSiv;
use McAfee\Services\MountNewAttributeFromMcAfee;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Factories\ServicesFactory;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;
use TradeAppOne\Exceptions\BusinessExceptions\OperationNoExists;
use TradeAppOne\Exceptions\BusinessExceptions\OperatorNoExists;
use TradeAppOne\Tests\Helpers\ControleBoletoHelper;
use TradeAppOne\Tests\Helpers\ControleFacilHelper;
use TradeAppOne\Tests\Helpers\MobileSecurityHelper;
use TradeAppOne\Tests\TestCase;

class ServiceFactoryTest extends TestCase
{
    use ControleBoletoHelper, ControleFacilHelper, MobileSecurityHelper;

    /** @test */
    public function should_return_IncompleteDataException_when_customer_no_sent()
    {
        $service      = [
            'operator'    => 'CLARO',
            'operation'   => 'CONTROLE_BOLETO',
            'msisdn'      => '',
            'dueDate'     => '',
            'invoiceType' => '',
            'areaCode'    => ''
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertFalse($serviceBuild->validate());
    }

    /** @test */
    public function should_return_controle_boleto_when_customer_all_sent()
    {
        $service      = [
            'operator'    => 'CLARO',
            'operation'   => 'CONTROLE_BOLETO',
            'msisdn'      => '',
            'dueDate'     => '',
            'invoiceType' => '',
            'areaCode'    => '',
            'customer'    => $this->controleBoletoCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_invalid()
    {
        $service = [
            'operator'  => 'TESTE',
            'operation' => 'CONTROLE_BOLETO',
        ];
        $this->expectException(OperatorNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
    }

    /** @test */
    public function should_return_exception_when_operation_invalid()
    {
        $service = [
            'operator'  => 'CLARO',
            'operation' => 'TESTE',
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
    }

    /** @test */
    public function should_return_exception_when_operation_and_operator_invalid()
    {
        $service = [
            'operator'  => 'TESTE',
            'operation' => 'TESTE',
        ];
        $this->expectException(OperatorNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
    }

    /** @test */
    public function should_return_service_CONTROLE_BOLETO_when_operator_CLARO_and_operation_CONTROLE_BOLETO()
    {
        $service      = [
            'operator'    => 'CLARO',
            'operation'   => 'CONTROLE_BOLETO',
            'invoiceType' => 1,
            'msisdn'      => '',
            'dueDate'     => '',
            'areaCode'    => '',
            'customer'    => $this->controleBoletoCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_BOLETO_with_controle_facil_customer(
    )
    {
        $service      = [
            'operator'    => 'CLARO',
            'operation'   => 'CONTROLE_BOLETO',
            'invoiceType' => 1,
            'msisdn'      => '',
            'dueDate'     => '',
            'areaCode'    => '',
            'customer'    => $this->controleFacilCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertFalse($serviceBuild->validate());
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_BOLETO_without_customer()
    {
        $service      = [
            'operator'    => 'CLARO',
            'operation'   => 'CONTROLE_BOLETO',
            'invoiceType' => 1,
            'msisdn'      => '',
            'dueDate'     => '',
            'areaCode'    => '',
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertFalse($serviceBuild->validate());
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_BOLETO_with_mobile_security_customer(
    )
    {
        $service      = [
            'operator'    => 'CLARO',
            'operation'   => 'CONTROLE_BOLETO',
            'invoiceType' => 1,
            'msisdn'      => '',
            'dueDate'     => '',
            'areaCode'    => '',
            'customer'    => $this->mobileSecurityCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertFalse($serviceBuild->validate());
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_CONTROLE_FACIL_when_operator_CLARO_and_operation_CONTROLE_FACIL()
    {
        $service      = [
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_FACIL',
            'msisdn'    => '1198173994',
            'customer'  => $this->controleFacilCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_FACIL_set_SUBMITTED_status()
    {
        $service      = [
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_FACIL',
            'msisdn'    => '1198173994',
            'customer'  => $this->controleFacilCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        $serviceBuild->setStatus(ServiceStatus::SUBMITTED);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_FACIL_set_REJECTED_status()
    {
        $service      = [
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_FACIL',
            'msisdn'    => '1198173994',
            'customer'  => $this->controleFacilCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        $serviceBuild->setStatus(ServiceStatus::REJECTED);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_FACIL_set_invalid_status()
    {
        $service = [
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_FACIL',
            'msisdn'    => '1198173994',
            'customer'  => $this->controleFacilCustomer
        ];
        $this->expectException(InvalidServiceStatus::class);
        $serviceBuild = ServicesFactory::make($service);
        $serviceBuild->setStatus('AA');
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_CONTROLE_FACIL_without_customer()
    {
        $service      = [
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_FACIL',
            'msisdn'    => '1198173994',
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertFalse($serviceBuild->validate());
    }

    /** @test */
    public function should_return_exception_when_operator_CLARO_and_operation_MOBILE_SECURITY()
    {

        $service = [
            'operator'  => 'CLARO',
            'operation' => 'MOBILE_SECURITY',
            'msisdn'    => '1198173994',
            'customer'  => $this->controleFacilCustomer
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_MOBILE_SECURITY_when_operator_MCAFEE_and_operation_MOBILE_SECURITY()
    {
        $mount = \Mockery::mock(MountNewAttributeFromMcAfee::class)->makePartial();
        $mount->shouldReceive('getAttributes')->andReturn([]);

        $this->app->singleton(MountNewAttributeFromMcAfee::class, function () use ($mount) {
            return $mount;
        });

        $service      = [
            'operator'  => Operations::MCAFEE,
            'operation' => Operations::MCAFEE_MOBILE_SECURITY,
            'product'   => '1343-93222-mmsu',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MCAFEE_and_operation_CONTROLE_BOLETO()
    {
        $service = [
            'operator'  => 'MCAFEE',
            'operation' => 'CONTROLE_BOLETO',
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MCAFEE_and_operation_CONTROLE_FACIL()
    {
        $service = [
            'operator'  => 'MCAFEE',
            'operation' => 'CONTROLE_FACIL',
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_ROUBO_FURTO_when_operator_MAPFRE_and_operation_ROUBO_FURTO()
    {
        $service      = [
            'operator'  => 'MAPFRE',
            'operation' => 'ROUBO_FURTO',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MAPFRE_and_operation_CONTROLE_BOLETO()
    {
        $service = [
            'operator'  => 'MAPFRE',
            'operation' => 'CONTROLE_BOLETO',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MAPFRE_and_operation_CONTROLE_FACIL()
    {
        $service = [
            'operator'  => 'MAPFRE',
            'operation' => 'CONTROLE_FACIL',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MAPFRE_and_operation_CLARO_PRE()
    {
        $service = [
            'operator'  => 'MAPFRE',
            'operation' => 'CLARO_PRE',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_CLARO_PRE_when_operator_CLARO_and_operation_CLARO_PRE()
    {
        $factory  = \Illuminate\Database\Eloquent\Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/clarobr/Factories/')
        );
        $claroPre = $factory->of(ClaroPre::class)->make()->toArray();

        $serviceBuild = ServicesFactory::make($claroPre);
        self::assertInstanceOf(ClaroPre::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MAPFRE_and_operation_CLARO_POS()
    {
        $service = [
            'operator'  => 'MAPFRE',
            'operation' => 'CLARO_POS',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_exception_when_operator_MCAFEE_and_operation_CLARO_POS()
    {
        $service = [
            'operator'  => 'MCAFEE',
            'operation' => 'CLARO_POS',
            'customer'  => $this->mobileSecurityCustomer
        ];
        $this->expectException(OperationNoExists::class);
        $serviceBuild = ServicesFactory::make($service);
        self::assertInstanceOf(Service::class, $serviceBuild);
    }

    /** @test */
    public function should_return_CLARO_BANDA_LARGA_when_operator_CLARO_and_operation_CLARO_BANDA_LARGA()
    {
        $factory         = \Illuminate\Database\Eloquent\Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/clarobr/Factories/')
        );
        $claroBandaLarga = $factory->of(ClaroBandaLarga::class)->make()->toArray();

        $serviceBuild = ServicesFactory::make($claroBandaLarga);

        $serviceBuild->validate();
        self::assertInstanceOf(ClaroBandaLarga::class, $serviceBuild);
    }

    /** @test */
    public function should_return_CLARO_POS_when_operator_CLARO_and_operation_CLARO_POS()
    {
        $factory  = \Illuminate\Database\Eloquent\Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/clarobr/Factories/')
        );
        $claroPos = $factory->of(ClaroPos::class)->make()->toArray();

        $serviceBuild = ServicesFactory::make($claroPos);

        $serviceBuild->validate();
        self::assertInstanceOf(ClaroPos::class, $serviceBuild);
    }

    protected function setUp()
    {
        parent::setUp();
        $mock = $this->getMockBuilder(MountNewAttributesService::class)
            ->setMethods(['getAttributes'])->getMock();
        $mock->method('getAttributes')->will($this->returnValue([]));
        $this->app->bind(MountNewAttributeFromSiv::class, function () use ($mock) {
            return $mock;
        });

    }
}
