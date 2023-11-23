<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBROrderApprovalException extends ThirdPartyExceptions
{
    protected $message;
    protected $statusCode;

    public function __construct(int $statusCode = Response::HTTP_BAD_REQUEST, ?string $message = null)
    {
        if ($message !== null) {
            $this->message = $message;
        }
        $this->statusCode = $statusCode;
    }

    public function getHttpStatus(): int
    {
        return $this->statusCode;
    }

    public function getShortMessage(): string
    {
        return 'TimBROrderApprovalException';
    }

    public function getDescription(): string
    {
        return $this->message ?? 'Erro TIM';
    }
}
