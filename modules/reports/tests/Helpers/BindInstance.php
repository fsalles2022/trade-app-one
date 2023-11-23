<?php

namespace Reports\Tests\Helpers;

use Mockery\Mock;

trait BindInstance
{
    /**
     * @return Mock
     */
    private function bindInstance($class)
    {
        $printer = \Mockery::mock($class)->makePartial();
        $this->app->instance($class, $printer);
        return $printer;
    }
}
