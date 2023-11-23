<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;

class ServiceTimControleFlexSeeder extends Seeder
{
    public function run(): void
    {
        Service::updateOrCreate(
            [
                'sector'    => Operations::LINE_ACTIVATION,
                'operator'  => Operations::TIM,
                'operation' => Operations::TIM_CONTROLE_FLEX,
            ]
        );
    }
}
