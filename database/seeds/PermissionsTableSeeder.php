<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param string $slug
     * @param string $label
     * @param string $client
     * @return void
     */
    public function run(string $slug, string $label, string $client): void
    {
        $builder = DB::table('permissions');

        $builder->where('slug', $slug)->exists() ?: $builder->insert([
            'slug' => $slug,
            'label' => $label,
            'client' => $client
        ]);
    }
}
