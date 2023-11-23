<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\tests\ServerMock\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class CasaEVideoHookApiResponse implements CasaEVideoApiMockInteface
{
    public const CPF_FAILED = '65458719000';

    public static function make(): CasaEVideoApiMockInteface
    {
        return new static();
    }

    public function getMock(RequestInterface $request): Response
    {
        if ($this->checkIfCPFSalesManToFail($request)) {
            return new Response(
                500,
                ['Content­Type' => 'application/json']
            );
        }

        return new Response(
            200,
            ['Content­Type' => 'application/json']
        );
    }

    private function checkIfCPFSalesManToFail(RequestInterface $request): bool
    {
        $payload = json_decode($request->getBody()->getContents(), true);
        return ($payload['vendedor']['cpf'] ?? null) === self::CPF_FAILED;
    }
}
