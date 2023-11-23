<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->getServices() as $service) {
            Service::updateOrCreate($service);
        }
    }

    /** @return array[] */
    private function getServices(): array
    {
        return [
            [
                'sector'    => Operations::LINE_ACTIVATION,
                'operator'  => Operations::CLARO,
                'operation' => Operations::CLARO_PRE_EXTERNAL_SALE,
            ],
            [
                'sector'    => Operations::LINE_ACTIVATION,
                'operator'  => Operations::TIM,
                'operation' => Operations::TIM_BLACK,
            ],
            [
                'sector'    => Operations::LINE_ACTIVATION,
                'operator'  => Operations::TIM,
                'operation' => Operations::TIM_BLACK_EXPRESS,
            ],
            [
                'sector'    => Operations::LINE_ACTIVATION,
                'operator'  => Operations::TIM,
                'operation' => Operations::TIM_BLACK_MULTI,
            ],
            [
                'sector'    => Operations::LINE_ACTIVATION,
                'operator'  => Operations::TIM,
                'operation' => Operations::TIM_BLACK_MULTI_DEPENDENT,
            ]
        ];
    }
}
