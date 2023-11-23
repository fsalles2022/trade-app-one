<?php

namespace ClaroBR\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;

trait SivFactoriesHelper
{
    public function sivFactories(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/clarobr/Factories/')
        );
    }
}
