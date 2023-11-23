<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardTradeInPermissionSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        DB::table('permissions')->insert([
            'label' => 'Adiciona permissão ao dashboard TRADEIN do powerBi',
            'slug' => 'DASHBOARD_TRADEIN.VIEW',
            'client' => 'WEB'
        ]);
    }
}
