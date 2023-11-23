<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Psr\Http\Message\RequestInterface;
use SurfPernambucanas\Enumerators\PagtelResponseCode;

class SubscriberActivateApiMock implements PagtelApiMockInterface
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

        $iccid = data_get($body, 'ICCID');

        if ($iccid === '89550000000000000001') {
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
            'msg'  => 'Registro realizado com sucesso.',
        ]);
    }

    protected function errorBody(): string
    {
        return json_encode([
            'code' => 'P04',
            'msg'  => 'Solicitação de Registro já enviada.',
        ]);
    }
}
