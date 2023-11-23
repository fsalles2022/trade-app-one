<?php

namespace ClaroBR\Tests\Unit\Domain\Services\UpdateAttributes;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateDependentsService;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\TestCase;

class ClaroBRUpdateDependentsServiceTest extends TestCase
{
    use SivFactoriesHelper;

    /** @test */
    public function should_call_when_network_option_sent()
    {
        $saleService = \Mockery::mock(SaleService::class)->makePartial();
        $saleService->shouldReceive('getByNetworkSlug')->once()->andReturn(new Collection());
        $connection = \Mockery::mock(SivConnection::class)->makePartial();

        $updateAttributes = new ClaroBRUpdateDependentsService($saleService, $connection);
        $updateAttributes->update(['network' => ['aaa']]);
    }
}
