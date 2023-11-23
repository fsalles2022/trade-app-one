<?php

namespace Core\WebHook\Tests\Unit;

use Core\WebHook\Jobs\WebHookJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class WebHookServiceObserverTest extends TestCase
{
    /** @test */
    public function should_dispatch_job_when_update_services(): void
    {
        App::shouldReceive('environment')->andReturn(Environments::PRODUCTION);
        Queue::fake();

        $service = factory(Service::class)->make();
        $sale    = SaleBuilder::make()->withServices($service)->build();

        $sale->services->first()->update([
            'status' => ServiceStatus::APPROVED
        ]);

        // TODO WebHookServiceObserver.php remove comments and assert one
        Queue::assertPushedTimes(WebHookJob::class, 0);
    }

    /** @test */
    public function should_not_dispatch_job_when_env_is_test(): void
    {
        Queue::fake();

        $service = factory(Service::class)->make();
        $sale    = SaleBuilder::make()->withServices($service)->build();

        $sale->services->first()->update([
            'status' => ServiceStatus::APPROVED
        ]);

        Queue::assertPushedTimes(WebHookJob::class, 0);
    }

    /** @test */
    public function should_not_dispatch_job_when_not_have_changes(): void
    {
        App::shouldReceive('environment')->andReturn(Environments::BETA);
        Queue::fake();

        $service = factory(Service::class)->make();
        $sale    = SaleBuilder::make()->withServices($service)->build();

        $sale->services->first()->update([]);

        Queue::assertPushedTimes(WebHookJob::class, 0);
    }
}
