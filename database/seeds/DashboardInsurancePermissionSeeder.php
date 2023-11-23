<?php

use Illuminate\Database\Seeder;

class DashboardInsurancePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'label' => 'Adiciona permissão ao dashboard INSURANCE do powerBi',
            'slug' => 'DASHBOARD_INSURANCE.VIEW',
            'client' => 'WEB'
        ]);
    }
}
