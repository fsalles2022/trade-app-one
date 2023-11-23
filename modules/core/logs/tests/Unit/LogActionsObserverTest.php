<?php

namespace Core\Logs\tests\Unit;

use Core\Logs\Jobs\LogActionsJob;
use Discount\Enumerators\DiscountStatus;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Tests\TestCase;

class LogActionsObserverTest extends TestCase
{
    /** @test */
    public function should_dispatch_queue_when_model_is_created_and_updated(): void
    {
        App::shouldReceive('environment')->andReturn(Environments::PRODUCTION);

        Queue::fake();
        Queue::assertNothingPushed();

        $discount = (new DiscountBuilder())->withStatus(DiscountStatus::ACTIVE)->build(); //1x
        $discount->update(['status' => DiscountStatus::INACTIVE]); //2x

        $discount->fill(['status' => DiscountStatus::ACTIVE]);
        $discount->save(); //3x

        Queue::assertPushedTimes(LogActionsJob::class, 3);
    }

    /** @test */
    public function should_not_dispatch_queue_when_model_has_not_changes(): void
    {
        App::shouldReceive('environment')->andReturn(Environments::PRODUCTION);

        Queue::fake();
        Queue::assertNothingPushed();

        $discount = (new DiscountBuilder())->build();

        $discount->update([]);

        Queue::assertPushedTimes(LogActionsJob::class, 1);
    }
}
