<?php

declare(strict_types=1);

namespace TimBR\Tests\ServerTest\TimCommissioningResponses;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class SendSaleToCommissioningResponse implements TimCommissioningResponseInterface
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
        return '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                    <soap:Body>
                        <IntegracaoResponse xmlns="http://tempuri.org/">
                            <IntegracaoResult>[{"cod_importacao":36,"st_imei":"","st_telefone":2413741,"erro":"sem erro, importado com sucesso."}]</IntegracaoResult>
                        </IntegracaoResponse>
                    </soap:Body>
                </soap:Envelope>';
    }
}
