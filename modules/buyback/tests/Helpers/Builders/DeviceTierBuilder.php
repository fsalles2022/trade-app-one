<?php

namespace Buyback\Tests\Helpers\Builders;

use Illuminate\Support\Facades\DB;

class DeviceTierBuilder
{
    public function build()
    {
        return DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);
    }
}
