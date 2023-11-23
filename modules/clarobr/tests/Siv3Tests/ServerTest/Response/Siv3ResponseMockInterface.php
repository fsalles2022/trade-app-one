<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

interface Siv3ResponseMockInterface
{
    /** @return mixed */
    public static function make();

    /**
     * @param Request $request
     * @return Response
     */
    public function getMock(Request $request): Response;
}
