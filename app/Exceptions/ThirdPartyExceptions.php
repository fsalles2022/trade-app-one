<?php

namespace TradeAppOne\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

abstract class ThirdPartyExceptions extends CustomRuleExceptions
{
    public function render()
    {
        Log::info('third-party', ['exception' => $this->getError()]);
        return response(['errors' => [$this->getError()]], $this->getHttpStatus());
    }

    public function getError()
    {
        $this->message = $this->message ?? trans('exceptions.third_party.default');
        return [
            'shortMessage'       => $this->getShortMessage(),
            'message'            => $this->getMessage(),
            'description'        => $this->getDescription(),
            'help'               => $this->getHelp(),
            'transportedMessage' => $this->getTransportedMessage(),
        ];
    }

    public function getHttpStatus()
    {
        return Response::HTTP_MISDIRECTED_REQUEST;
    }
}
