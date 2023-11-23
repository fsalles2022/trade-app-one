<?php

namespace TradeAppOne\Tests\Helpers\Traits;

trait TestDebug
{
    public function getDebug(...$args)
    {
        $response = $this->get(...$args);
        dd($response);
    }

    public function getDebugJson(...$args)
    {
        $response = $this->get(...$args);
        dd($response->json());
    }
}