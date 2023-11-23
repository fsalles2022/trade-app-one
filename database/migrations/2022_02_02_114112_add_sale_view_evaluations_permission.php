<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AddSaleViewEvaluationsPermission extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'SaleViewEvaluationsPermissionSeeder',
            '--force' => true
        ]);
    }

    public function down(): void
    {
        DB::unprepared("delete from permissions where slug = '" . Permissions::SALE_VIEW_EVALUATIONS . "'");
    }
}
