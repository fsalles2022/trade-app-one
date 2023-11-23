<?php

namespace TimBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBROrder extends ThirdPartyExceptions
{
    /** @var Responseable */
    protected $response;

    /** @var int|mixed */
    protected $statusCode;

    /** @var string|null */
    protected $message;

    public function __construct(Responseable $response, $statusCode = Response::HTTP_BAD_REQUEST, ?string $message = null)
    {
        $this->response   = $response;
        $this->statusCode = $statusCode;

        if ($message !== null) {
            $this->message = $message;
        }
    }

    public function getHttpStatus(): int
    {
        return $this->statusCode;
    }

    public function getShortMessage(): string
    {
        return 'BusinessException';
    }

    public function getDescription(): string
    {
        return $this->message ?? 'Erro TIM';
    }

    public function getResponse(): Responseable
    {
        return $this->response;
    }
}
