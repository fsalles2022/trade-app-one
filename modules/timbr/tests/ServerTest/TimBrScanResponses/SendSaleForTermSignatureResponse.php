<?php

declare(strict_types=1);

namespace TimBR\Tests\ServerTest\TimBrScanResponses;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class SendSaleForTermSignatureResponse implements TimBrScanResponseInterface
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
            "mensagem" => "Dados recebidos com sucesso",
        ]);
    }
}
