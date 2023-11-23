<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevicesSeeder extends Seeder
{
    public function run()
    {
        $device = DB::table('devices')->count();

        if ($device === 0) {
            $path = __DIR__ . '/dump/devices.sql';
            $sql  = file_get_contents($path);
            DB::unprepared($sql);
        }
    }
}
