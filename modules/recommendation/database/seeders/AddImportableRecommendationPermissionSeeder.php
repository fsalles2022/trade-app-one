<?php

namespace Recommendation\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddImportableRecommendationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            'label'  => 'Adiciona permissão para fazer importação de pessoas que indicarão vendas.',
            'slug'   => 'IMPORTABLE.SALE_RECOMMENDATION',
            'client' => 'WEB'
        ]);
    }
}
