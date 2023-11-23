<?php

namespace NextelBR\Adapters\Response;

use NextelBR\Connection\M4uModal\NextelBRModalRoutes;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class ModalLinResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        if ($authCode = data_get($originalResponse->toArray(), 'authCode')) {
            $content['link'] = NextelBRModalRoutes::uriModal($authCode);
            $this->adapted   = $content;
        } else {
            $this->pushError($originalResponse->toArray());
        }
    }
}
