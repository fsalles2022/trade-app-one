<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Illuminate\Support\Facades\Queue;
use TradeAppOne\Domain\Services\SyncUserOperatorsService;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Jobs\VivoSyncUsersJob;

class SyncUserOperatorsServiceTest extends TestCase
{
    /** @test */
    public function should_dispatch_job_sync_user()
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        Queue::fake();
        Queue::assertNothingPushed();

        $this->syncService()->sync($user, $pointOfSale);

        Queue::assertPushed(VivoSyncUsersJob::class, 1);
    }

    /** @test */
    public function should_dispatch_job_sync_user_when_changes_is_attached()
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $changes = $user->pointsOfSale()->sync($pointOfSale);

        Queue::fake();
        Queue::assertNothingPushed();

        $this->syncService()->sync($user, $pointOfSale, $changes);

        Queue::assertPushed(VivoSyncUsersJob::class, 1);
    }

    /** @test */
    public function should_not_dispatch_job_sync_user_when_changes_attach_is_empty()
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        $changes = $user->pointsOfSale()->sync($pointOfSale);

        Queue::fake();
        Queue::assertNothingPushed();

        $this->syncService()->sync($user, $pointOfSale, $changes);

        Queue::assertNothingPushed();
    }

    private function syncService(): SyncUserOperatorsService
    {
        return resolve(SyncUserOperatorsService::class);
    }
}