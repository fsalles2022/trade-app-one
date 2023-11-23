<?php

declare(strict_types=1);

namespace SurfPernambucanas\Database\Seed;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;

class ServicesSmartControlSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        Service::updateOrCreate([
            'sector'    => Operations::LINE_ACTIVATION,
            'operator'  => Operations::SURF_PERNAMBUCANAS,
            'operation' => Operations::SURF_PERNAMBUCANAS_SMART_CONTROL,
        ]);
    }
}
