<?php

namespace TradeAppOne\Tests;

use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use TradeAppOne\Http\Middleware\CheckMultipleLoginPerUser;
use TradeAppOne\Tests\Helpers\Migrations\SqliteMigration;
use TradeAppOne\Tests\Helpers\Traits\TestDebug;
use TradeAppOne\Tests\Helpers\Traits\WithoutHeimdall;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithoutHeimdall, TestDebug;

    protected $migrationNotRunned = true;

    public function tearDown()
    {
        parent::tearDown();
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
    }

    protected function setUp()
    {
        parent::setUp();
        $this->disableHeimdallForAllTests();
        $this->withoutMiddleware(CheckMultipleLoginPerUser::class);
        try {
            $migration = new SqliteMigration;
            $migration->down();
            if ($this->migrationNotRunned) {
                $migration->up();
                $this->migrationNotRunned = false;
            }
            Schema::connection('mongodb')->drop('sales');
            Schema::connection('mongodb')->drop('customers');
            Schema::connection('mongodb')->drop('offerDeclined');
        } catch (Exception $exception) {
        }
    }
}
