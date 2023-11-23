<?php

namespace TradeAppOne\Tests\Helpers\Traits;

use TradeAppOne\Domain\Logging\Heimdall\HeimdallConcrete;

trait WithoutHeimdall
{
    public function disableHeimdallForAllTests()
    {
        $mock = \Mockery::mock(HeimdallConcrete::class)->makePartial();
        $mock->shouldReceive('fire');
        app()->instance(HeimdallConcrete::class, $mock);
    }
}
