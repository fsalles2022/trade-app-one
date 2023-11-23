<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Enumerators\PagtelResponseCode;

class AddCardApiMock implements PagtelApiMockInterface
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
        $body = $this->deserializeRequestBody($request);

        $cardNumber = data_get($body, 'cardNumber');

        if ($cardNumber === '5506775588250071') {
            return $this->successResponse($this->errorBody());
        }
        
        return $this->successResponse($this->sucessBody());
    }

    protected function successResponse(string $body): Response
    {
        return new Response(
            HttpResponse::HTTP_OK,
            ['Content­Type' => 'application/json'],
            $body
        );
    }

    protected function sucessBody(): string
    {
        return json_encode([
            'code' => PagtelResponseCode::SUCCESS,
            'msg'  => 'Sucesso',
            'paymentID' => "5",
        ]);
    }

    protected function errorBody(): string
    {
        return json_encode([
            'code' => 'P01',
            'msg'  => 'Ops! Este cartão já esta em uso',
        ]);
    }
}
