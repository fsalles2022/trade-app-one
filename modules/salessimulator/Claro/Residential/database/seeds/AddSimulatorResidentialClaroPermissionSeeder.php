<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\database\seeds;

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class AddSimulatorResidentialClaroPermissionSeeder extends Seeder
{
    public const SLUG = 'SALES_SIMULATOR.CREATE';

    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => self::SLUG,
            ],
            [
                'slug' => self::SLUG,
                'label' => 'Simular vendas residenciais',
                'client' => SubSystemEnum::WEB
            ]
        );
    }
}
