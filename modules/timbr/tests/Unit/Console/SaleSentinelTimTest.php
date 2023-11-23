<?php

namespace TimBR\Tests\Unit\Console;

use Illuminate\Support\Facades\Artisan;
use TimBR\Services\TimBRSentinel;
use TradeAppOne\Tests\TestCase;

class SaleSentinelTimTest extends TestCase
{
    /** @test */
    public function should_be_registry_daily_command(): void
    {
        $mock = \Mockery::mock(TimBRSentinel::class)->makePartial();
        $mock->shouldReceive('sentinelDailySalesByProtocol')->once();

        $this->app->singleton(TimBRSentinel::class, static function () use ($mock) {
            return $mock;
        });
        Artisan::call('sentinel:tim', ['--daily' => true]);
    }

    /** @test */
    public function should_be_registry_yearly_command(): void
    {
        $mock = \Mockery::mock(TimBRSentinel::class)->makePartial();
        $mock->shouldReceive('sentinelYearlySalesByProtocol')->once();

        $this->app->singleton(TimBRSentinel::class, static function () use ($mock) {
            return $mock;
        });
        Artisan::call('sentinel:tim', ['--yearly' => true]);
    }

    /** @test */
    public function should_be_registry_all_command(): void
    {
        $mock = \Mockery::mock(TimBRSentinel::class)->makePartial();
        $mock->shouldReceive('sentinelGetAllSalesByProtocol')->once();

        $this->app->singleton(TimBRSentinel::class, static function () use ($mock) {
            return $mock;
        });
        Artisan::call('sentinel:tim', ['--all' => true]);
    }
}
