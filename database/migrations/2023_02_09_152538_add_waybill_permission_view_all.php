<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaybillPermissionViewAll extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => WaybillPermissionViewAllSeeder::class,
            '--force' => true
        ]);
    }

    public function down():void
    {
        DB::unprepared('delete from permissions where slug = ?', [WaybillPermission::getFullName(WaybillPermission::VIEW_ALL)]);
    }
}
