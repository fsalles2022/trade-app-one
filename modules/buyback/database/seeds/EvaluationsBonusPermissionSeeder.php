<?php


namespace Buyback\database\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationsBonusPermissionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            'label' => 'Adiciona permissão a central de importação de Incremento de Bonus no Trade In.',
            'slug' => 'IMPORTABLE.EVALUATIONS_BONUS',
            'client' => 'WEB',
        ]);
    }
}
