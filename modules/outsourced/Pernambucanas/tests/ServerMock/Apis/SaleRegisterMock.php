<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\tests\ServerMock\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use SurfPernambucanas\Tests\ServerTest\Apis\RequestUtils;

class SaleRegisterMock implements PernambucanasApiMockInterface
{
    use RequestUtils;

    public static function make(): PernambucanasApiMockInterface
    {
        return new static();
    }

    public function getMock(RequestInterface $request): Response
    {
        return new Response(
            204,
            ['Content­Type' => 'application/json'],
            $this->successBody()
        );
    }

    public function successBody(): string
    {
        return $this->getResponseJsonByFilePath(__DIR__ . '/Responses/sendSuccess.json');
    }
}
