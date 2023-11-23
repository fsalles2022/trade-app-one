<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use TradeAppOne\Domain\Enumerators\Operations;

class InsertTimBlackMultiDependentService extends Migration
{
    public function up(): void
    {
        Artisan::call(
            'db:seed',
            [
                '--class' => ServicesSeeder::class,
                '--force' => true,
            ]
        );
    }

    public function down(): void
    {
        DB::unprepared("delete from services where operation = '" . Operations::TIM_BLACK_MULTI_DEPENDENT . "'");
    }
}
