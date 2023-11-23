<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\tests\ServerMock\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

interface CasaEVideoApiMockInteface
{
    public static function make(): self;

    public function getMock(RequestInterface $request): Response;
}
