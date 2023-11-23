<?php
namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;
use TradeAppOne\Tests\TestCase;

class UpdateSalesStatusTimTest extends TestCase
{
    /** @test */
    public function should_be_registred()
    {
        Artisan::call('tim:update-sale-status', ['status' => 'APPROVED', 'protocols' => ['1']]);
    }

    /** @test */
    public function should_return_invalid_service_status_when_status_not_valid()
    {
        $this->expectException(InvalidServiceStatus::class);
        Artisan::call('tim:update-sale-status', ['status' => 'INVALID', 'protocols' => ['1']]);
    }
}
