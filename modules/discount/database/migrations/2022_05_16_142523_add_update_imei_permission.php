<?php

declare(strict_types=1);

use Discount\database\seeds\UpdateImeiPermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddUpdateImeiPermission extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => UpdateImeiPermissionSeeder::class,
            '--force' => true
        ]);
    }

    public function down(): void
    {
    }
}
