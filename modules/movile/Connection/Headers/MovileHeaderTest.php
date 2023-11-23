<?php

namespace Movile\Connection\Headers;

use Movile\Exceptions\MovileSecuritySignatureException;
use TradeAppOne\Tests\TestCase;

class MovileHeaderTest extends TestCase
{
    /** @test */
    public function shoul_return_array_with_headers()
    {
        $body = '{"id":"a7d23226-bdfc-4d85-a30a-b2d7eccc36e6","phone_number":"5511973512530","sku":"com.movile.cubes.br.biweekly.homolog","origin":"trade_up_group","application_id":437}';

        $headers = (new MovileHeader())->getHeaders($body);

        self::assertTrue(is_array($headers));
    }

    /** @test */
    public function should_return_array_with_headers_when_json_invalid()
    {
        $body = '{ss';

        $this->expectException(MovileSecuritySignatureException::class);

        $headers = (new MovileHeader())->getHeaders($body);
    }
}
