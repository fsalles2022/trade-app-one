<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/** Interface used to assign contract to create mocks APIs from Pagtel */
interface PagtelApiMockInterface
{
    /** init mock */
    public static function make();

    /** Logic to mock */
    public function getMock(RequestInterface $request): Response;
}
