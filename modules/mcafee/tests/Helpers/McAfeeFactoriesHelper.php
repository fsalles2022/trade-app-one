<?php

namespace McAfee\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;

trait McAfeeFactoriesHelper
{
    public function mcAfeeFactories(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/mcafee/Factories/')
        );
    }
}
