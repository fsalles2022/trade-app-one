<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class PlansApiMock implements PagtelApiMockInterface
{
    use RequestUtils;

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
        return $this->getResponseJsonByFilePath(__DIR__ . '/Responses/Plans/success.json');
    }
}
