<?php

namespace TradeAppOne\Tests\Helpers\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;

trait ArrayAssertTrait
{
    public function assertArrayStructure($resultData = null, array $structure = null)
    {
        return (new TestResponse(new Response))->assertJsonStructure($structure, $resultData);
    }
}
