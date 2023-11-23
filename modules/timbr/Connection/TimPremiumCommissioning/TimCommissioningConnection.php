<?php

declare(strict_types=1);

namespace TimBR\Connection\TimPremiumCommissioning;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimCommissioningConnection
{
    /** @var TimCommissioningHttpClient */
    private $commissioningHttpClient;

    public function __construct(TimCommissioningHttpClient $commissioningHttpClient)
    {
        $this->commissioningHttpClient = $commissioningHttpClient;
    }

    /** @param mixed[] $payload */
    public function send(array $payload): Responseable
    {
        $headers = [
            'Content-Type' => 'text/xml',
        ];

        $jsonEncoded = json_encode([$payload]);

        $xmlPayload = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
                <soap:Body>
                    <Integracao xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"http://tempuri.org/\">
                        <json>
                           $jsonEncoded
                        </json> 
                    </Integracao>
                </soap:Body>
            </soap:Envelope>";

        try {
            $response = $this->commissioningHttpClient->execute('post', TimCommissioningRoutes::SEND, ['headers' => $headers, 'body' => $xmlPayload]);

            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }
}
