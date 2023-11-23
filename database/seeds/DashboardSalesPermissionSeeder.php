<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardSalesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'label'  => 'Adiciona permissão ao dashboardSales do powerBi',
            'slug'   => 'DASHBOARD_MANAGEMENT.VIEW',
            'client' => 'WEB'
        ]);
    }
}
