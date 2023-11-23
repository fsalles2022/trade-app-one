<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

class AddPermissionNewSaleFlowClaroControleFacilV3 extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => ControleFacilV3SaleFlowPermissionsSeeder::class,
            '--force' => true
        ]);
    }

    public function down(): void
    {
        DB::unprepared('delete from permissions where slug = ' . ControleFacilV3SaleFlowPermissionsSeeder::getControleFacilV3Slug());
        DB::unprepared('delete from permissions where slug = ' . ControleFacilV3SaleFlowPermissionsSeeder::getControleFacilV3ActivationSlug());
        DB::unprepared('delete from permissions where slug = ' . ControleFacilV3SaleFlowPermissionsSeeder::getControleFacilV3MigrationSlug());
        DB::unprepared('delete from permissions where slug = ' . ControleFacilV3SaleFlowPermissionsSeeder::getControleFacilV3PortabilitySlug());
    }
}
