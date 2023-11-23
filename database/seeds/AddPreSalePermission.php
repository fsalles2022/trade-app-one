<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Models\Tables\Permission;

class AddPreSalePermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'slug' => 'SALE.CREATE_PRE_SALE',
            'label' => 'Criar ou Salvar uma Pré Venda',
            'client' => 'WEB'
        ]);
    }
}
