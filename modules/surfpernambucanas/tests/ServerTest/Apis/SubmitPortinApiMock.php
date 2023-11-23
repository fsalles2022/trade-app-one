<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use SurfPernambucanas\Enumerators\PagtelResponseCode;

class SubmitPortinApiMock implements PagtelApiMockInterface
{
    /** @inheritDoc */
    public static function make(): self
    {
        return new self();
    }

    /** @inheritDoc */
    public function getMock(RequestInterface $request): Response
    {
        return new Response(
            200,
            ['Content­Type' => 'application/json'],
            $this->sucessBody()
        );
    }

    protected function sucessBody(): string
    {
        return json_encode([
            'code' => PagtelResponseCode::SUCCESS,
            'msg'  => 'Sucesso',
            'MNPPI' => "MNPPI0000000308",
        ]);
    }
}
