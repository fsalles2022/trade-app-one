<?php


namespace FastShop\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class FastshopUnavailableException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'Fastshop']);
    }

    public function getShortMessage()
    {
        return 'FastShopApiUnavailable';
    }

    public function getHelp()
    {
        return trans('help.third_party_unavailable');
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_MISDIRECTED_REQUEST;
    }

    public function report()
    {
        Log::debug('third-party-api-fastshop', ['url' => 'offline']);
    }
}
