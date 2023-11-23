<?php

declare(strict_types=1);

namespace TimBR\Tests\ServerTest\TimBrScanResponses;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

interface TimBrScanResponseInterface
{
    /** @return mixed */
    public static function make();

    /**
     * @param Request $request
     * @return Response
     */
    public function getMock(Request $request): Response;
}
