<?php

declare(strict_types=1);

namespace TimBR\Tests\ServerTest\TimBrScanResponses;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class GenerateAuthenticateLinkResponse implements TimBrScanResponseInterface
{
    /** @inheritDoc */
    public static function make(): self
    {
        return new self();
    }

    /** @inheritDoc */
    public function getMock(Request $request): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->successBody()
        );
    }

    protected function successBody(): string
    {
        return json_encode([
            "link" => "https://hml-mobile-tim.brflow.com.br/web-tim-pdv-mobile/varejopremium/login/19anj7rmwso0g4o4kk8ss",
            "linkId" => "161",
        ]);
    }
}
