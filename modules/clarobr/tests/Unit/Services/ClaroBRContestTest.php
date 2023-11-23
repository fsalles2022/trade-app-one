<?php

namespace ClaroBR\Tests\Unit\Services;

use ClaroBR\Enumerators\SivStatus;
use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Services\ClaroBRContest;
use ClaroBR\Services\SivService;
use ClaroBR\Tests\Helpers\ClaroServices;
use Mockery;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\ServiceRepository;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBRContestTest extends TestCase
{
    /** @test */
    public function should_return_service_not_integrated_exception()
    {
        $this->expectException(ServiceNotIntegrated::class);

        (new ClaroBRContest(resolve(SivService::class), resolve(SaleService::class)))->contestService((new
        Service()), []);
    }

    /** @test */
    public function should_contest_service_return_service()
    {
        $user      = (new UserBuilder())->build();
        $serviceId = '2';

        $sivConnectionMock = Mockery::mock(SivService::class);
        $sivConnectionMock->expects()->contest($serviceId, $user->id)->andReturn(['status' => 'ATIVADO']);
        $sivConnectionMock
            ->shouldReceive('getSale')
            ->andReturn(collect([
                [
                    'id'       => $serviceId,
                    'services' => [
                        [
                            'id'     => $serviceId,
                            'status' =>
                                SivStatus::REJECTED[0]
                        ]
                    ]
                ]
            ]));

        $claroPos                      = ClaroServices::ClaroPos();
        $claroPos->status              = ServiceStatus::REJECTED;
        $claroPos->operatorIdentifiers = ['servico_id' => $serviceId];

        $sale    = (new SaleBuilder())->withUser($user)->withServices([$claroPos])->build();
        $service = $sale->services()->first();

        (new ClaroBRContest($sivConnectionMock, resolve(SaleService::class)))->contestService(
            $service,
            []
        );
    }

    /** @test */
    public function should_contest_service_return_service_changed()
    {
        $id = '2';

        $sivConnectionMock = Mockery::mock(SivService::class)->makePartial();
        $sivConnectionMock
            ->shouldReceive('getSale')
            ->andReturn(collect([['id' => $id, 'services' => [['id' => $id, 'status' => 'ATIVADO']]]]));
        $sivConnectionMock->shouldReceive('contest')->andReturn(['status' => 'ATIVADO']);

        $service                      = ClaroServices::ClaroPos();
        $service->status              = ServiceStatus::REJECTED;
        $service->operatorIdentifiers = [
            'servico_id' => $id,
            'venda_id'   => $id
        ];

        (new SaleBuilder())
            ->withServices([$service])
            ->build();

        $result = (new ClaroBRContest($sivConnectionMock, resolve(SaleService::class)))->contestService(
            $service,
            []
        );

        self::assertEquals($result['status'], ServiceStatus::ACCEPTED);
        self::assertEquals($result['statusThirdParty'], 'ATIVADO');
    }

    /** @test */
    public function should_contest_service_update_sale_to_approved_based_on_map_to_sales()
    {
        $serviceInstance         = new Service();
        $serviceInstance->status = ServiceStatus::REJECTED;

        $user                                 = (new UserBuilder())->build();
        $serviceId                            = '123';
        $serviceInstance->operatorIdentifiers = ['servico_id' => $serviceId];

        $sale    = (new SaleBuilder())->withUser($user)->withServices([$serviceInstance])->build();
        $service = $sale->services()->first();

        $sivConnectionMock = Mockery::mock(SivService::class);
        $sivConnectionMock
            ->shouldReceive('getSale')
            ->andReturn(collect([['id' => $serviceId, 'services' => [['id' => $serviceId, 'status' => 'INVALID']]]]));
        $sivConnectionMock->expects()->contest($serviceId, $user->id)->andReturn(['status' => 'INVALID']);

        $this->expectExceptionMessage(trans('siv::exceptions.' . ClaroExceptions::CONTEST_INVALID_RESPONSE));

        (new ClaroBRContest($sivConnectionMock, resolve(SaleService::class)))->contestService($service, []);
    }
}
