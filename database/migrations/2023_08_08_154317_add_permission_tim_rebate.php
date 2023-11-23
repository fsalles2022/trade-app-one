<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class AddPermissionTimRebate extends Migration
{
   public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => TimRebatePermissionSeeder::class,
            '--force' => true
        ]);
    }

    public function down(): void
    {
        DB::unprepared('delete from permissions where slug = ' . TimRebatePermissionSeeder::getSlug());
    }
}
