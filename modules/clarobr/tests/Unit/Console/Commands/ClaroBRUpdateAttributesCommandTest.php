<?php

namespace ClaroBR\Tests\Unit\Console\Commands;

use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateAttributes;
use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateDependentsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Tests\TestCase;

class ClaroBRUpdateAttributesCommandTest extends TestCase
{
    /** @test */
    public function should_register()
    {
        $mock = \Mockery::mock(ClaroBRUpdateAttributes::class)->makePartial();
        $mock->shouldReceive('update')->once()->andReturn(new Collection());
        $this->app->singleton(ClaroBRUpdateDependentsService::class, function () use ($mock) {
            return $mock;
        });
        Artisan::call('clarobr-update:dependents');
    }
}
