<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Enumerators\Permissions;

class SaleViewEvaluationsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->insert([
            'label' => 'Adiciona permissão para apresentar a avaliação técnica TradeIn nos detalhes da venda',
            'slug' => Permissions::SALE_VIEW_EVALUATIONS,
            'client' => 'WEB'
        ]);
    }
}
