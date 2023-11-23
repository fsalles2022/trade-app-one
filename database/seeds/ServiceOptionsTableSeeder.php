<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param string $action
     * @return void
     */
    public function run(string $action): void
    {
        $builder = DB::table('serviceOptions');

        $builder->where('action', $action)->exists() ?? $builder->insert(['action' => $action]);
    }
}
