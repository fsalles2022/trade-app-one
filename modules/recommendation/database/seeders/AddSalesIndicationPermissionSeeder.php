<?php

namespace Recommendation\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSalesIndicationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            'label'  => 'Adiciona permissão para o vendedor fazer indicação',
            'slug'   => 'SALE.INDICATION',
            'client' => 'WEB'
        ]);
    }
}
