<?php

namespace Movile\Connection\Headers;

use Movile\Exceptions\MovileSecuritySignatureException;
use TradeAppOne\Tests\TestCase;

class MovileSignatureTest extends TestCase
{
    /** @test */
    public function should_return_array_with_headers_when_json_invalid()
    {
        $body = '{ss';

        $this->expectException(MovileSecuritySignatureException::class);

        $headers = MovileSignature::generate($body);
    }

    /** @test */
    public function should_return_array_with_headers_when_json_valid()
    {
        $body = '{"json" : "valid"}';

        $signature = MovileSignature::generate($body);

        self::assertTrue(is_string($signature));
    }
}
