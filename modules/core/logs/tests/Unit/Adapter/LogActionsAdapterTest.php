<?php

namespace Core\Logs\tests\Unit\Adapter;

use Carbon\Carbon;
use Core\Logs\Adapters\LogActionsAdapter;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class LogActionsAdapterTest extends TestCase
{
    /** @test */
    public function should_return_structure_adapted()
    {
        $user = (new UserBuilder())->build();
        $log  = LogActionsAdapter::get($user);

        $this->assertArrayHasKey('request', $log);
        $this->assertArrayHasKey('date', $log);

        $this->assertId($user, $log);
        $this->assertTable($user, $log);
        $this->assertModel($user, $log);
    }

    /** @test */
    public function should_return_changes_of_date_with_carbon()
    {
        $date = '2019-10-30';
        $user = (new UserBuilder())->build();
        $user->fill(['updatedAt' => '2019-10-30']);
        $changes = LogActionsAdapter::getChanges($user);
        $this->assertEquals(Carbon::parse($date)->toIso8601String(), $changes['updatedAt']);
    }

    private function assertId($user, $log)
    {
        $expected = 'users_'.$user->id.'_'.time();
        $this->assertEquals($expected, $log['id']);
    }

    private function assertTable(User $user, $log)
    {
        $expected = [
            'id' => $user->getKey(),
            'name' => $user->getTable()
        ];

        $this->assertEquals($expected, $log['table']);
    }

    private function assertModel(User $user, array $log)
    {
        $expected =  [
            'original'    => json_encode($user->getOriginal()),
            'lazy_loaded' => json_encode($user->getRelations()),
            'changes'     => [],
        ];

        $this->assertEquals($expected, $log['model']);
    }
}
