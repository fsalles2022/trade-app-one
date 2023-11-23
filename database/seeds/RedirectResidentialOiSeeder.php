<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use TradeAppOne\Domain\Models\Tables\Service;

class RedirectResidentialOiSeeder extends Seeder
{    
    public function run(): void
    {
        $sector = 'LINE_ACTIVATION';
        $operator = 'OI';
        $operation = 'OI_RESIDENCIAL';
        $label = 'Oi Fibra Residencial';

        Service::updateOrCreate(
            [
                'sector'     => $sector,
                'operator'   => $operator,
                'operation'  => $operation,
                'label'     => $label
            ],
            [
                'sector'     => $sector,
                'operator'   => $operator,
                'operation'  => $operation,
                'label'     => $label,
                'createdAt' => Carbon::now(),
                'updatedAt' => Carbon::now()
            ]
        );          
    } 

}
