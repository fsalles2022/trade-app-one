<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardLadsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'label' => 'Adiciona permissão ao dashboardLads do powerBi',
            'slug' => 'DASHBOARD_LADS.VIEW',
            'client' => 'WEB'
        ]);

    }
}
