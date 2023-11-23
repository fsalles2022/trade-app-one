<?php

namespace TradeAppOne\Tests\Unit\Domain\Repositories\Filters;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Repositories\Filters\SalesFilter;
use TradeAppOne\Tests\TestCase;

class SalesFilterTest extends TestCase
{
    /** @test */
    public function should_build_name_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['name' => 'ROBISVALDO'];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['name'], $received['$or'][0]['services.customer.firstName']->getPattern());
        $this->assertEquals($filters['name'], $received['$or'][1]['services.customer.lastName']->getPattern());
    }

    /** @test */
    public function should_build_cpf_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['cpf' => '00000000000'];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['cpf'], $received['$or'][0]['user.cpf']->getPattern());
        $this->assertEquals($filters['cpf'], $received['$or'][1]['services.customer.cpf']->getPattern());
    }

    /** @test */
    public function should_build_cpfSalesman_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['cpfSalesman' => '00000000000'];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['cpfSalesman'], $received['user.cpf']->getPattern());
    }

    /** @test */
    public function should_build_cpfCustomer_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['cpfCustomer' => '00000000000'];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['cpfCustomer'], $received['services.customer.cpf']->getPattern());
    }

    /** @test */
    public function should_build_saleId_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['saleId' => '20190515142954323-0'];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['saleId'], $received['saleTransaction']->getPattern());
    }

    /** @test */
    public function should_build_operator_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['operator' => [Operations::TRADE_IN_MOBILE]];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['operator'], $received['services.operator']['$in']);
    }

    /** @test */
    public function should_build_imei_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['imei' => '75523273677584'];

        $received = $saleFilter->build($filters);


        $this->assertEquals($filters['imei'], $received['services.imei']);
    }

    /** @test */
    public function should_build_log_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['log' => '22930423'];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['log'], $received['$or'][0]['services.operatorIdentifiers.servico_id']);
        $this->assertEquals($filters['log'], $received['$or'][1]['services.operatorIdentifiers.venda_id']);
        $this->assertEquals($filters['log'], $received['$or'][2]['services.log.type']->getPattern());
        $this->assertEquals($filters['log'], $received['$or'][3]['services.log.message']->getPattern());
    }

    /** @test */
    public function should_build_pointsOfSale_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['pointsOfSale' => ['22696923000162']];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['pointsOfSale'], $received['pointOfSale.cnpj']['$in']);
    }

    /** @test */
    public function should_build_startDate_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['startDate' => '2019-05-17'];
        $startDate = Carbon::parse($filters['startDate'])->startOfDay();
        $gte = MongoDateHelper::dateTimeToUtc($startDate);

        $received = $saleFilter->build($filters);

        $this->assertEquals($gte, $received['createdAt']['$gte']);
    }

    /** @test */
    public function should_build_endDate_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['endDate' => '2019-05-17'];
        $endDate = Carbon::parse($filters['endDate']);
        $lt      = MongoDateHelper::dateTimeToUtc($endDate);

        $received = $saleFilter->build($filters);

        $this->assertEquals($lt, $received['createdAt']['$lt']);
    }


    /** @test */
    public function should_build_status_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['status' => [ServiceStatus::APPROVED]];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['status'], $received['services.status']['$in']);
    }

    /** @test */
    public function should_build_operation_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['operation' => Operations::TRADE_IN];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['operation'], $received['services.operation']->getPattern());
    }

    /** @test */
    public function should_build_ntc_filter()
    {
        $saleFilter = new SalesFilter();

        $filters  = ['ntc' => '11999999999'];
        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['ntc'], $received['$or'][0]['services.msisdn']->getPattern());
        $this->assertEquals($filters['ntc'], $received['$or'][1]['services.portedNumber']->getPattern());
        $this->assertEquals($filters['ntc'], $received['$or'][2]['services.log.numeroAcesso']->getPattern());
    }


    /** @test */
    public function should_build_mode_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['mode' => Modes::ACTIVATION];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['mode'], $received['services.mode']->getPattern());
    }

    /** @test */
    public function should_build_network_filter()
    {
        $saleFilter = new SalesFilter();

        $filters = ['networks' => [NetworkEnum::RIACHUELO]];

        $received = $saleFilter->build($filters);

        $this->assertEquals($filters['networks'], $received['pointOfSale.network.slug']['$in']);
    }

}
