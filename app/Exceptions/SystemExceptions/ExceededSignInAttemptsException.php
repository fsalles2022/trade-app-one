<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Exceptions\CustomRuleExceptions;

class ExceededSignInAttemptsException extends CustomRuleExceptions
{
    protected $credential;

    public function __construct(string $credential = "")
    {
        $this->credential = $credential;
    }

    public function getDescription()
    {
        return trans('exceptions.sign_in.exceeded_sign_in_attempts');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    public function report()
    {
        Log::info($this->getShortMessage(), ['credential' => $this->credential,]);
    }

    public function getShortMessage()
    {
        return 'ExceededSigInAttempts';
    }

    public function getHelp()
    {
        return trans('help.sign_in.exceeded_sign_in_attempts');
    }
}
