<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use SalesSimulator\Claro\Residential\database\seeds\AddSimulatorResidentialClaroPermissionSeeder;

class AddPermissionSimulatorResidentialClaro extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => AddSimulatorResidentialClaroPermissionSeeder::class,
            '--force' => true
        ]);
    }

    public function down(): void
    {
        //
    }
}
