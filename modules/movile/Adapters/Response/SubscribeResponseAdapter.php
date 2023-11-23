<?php

namespace Movile\Adapters\Response;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class SubscribeResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        $arrayResponse = $originalResponse->toArray();
        if ($this->isSuccess()) {
            $content['message']     = trans('movile::messages.subscription.success');
            $content['transported'] = $arrayResponse;
        } elseif (data_get($arrayResponse, 'message') == 'Error while performing sign up') {
            $content['message']     = trans('movile::messages.subscription.msisdn_invalid');
            $content['transported'] = $arrayResponse;
            $this->pushError($content);
        } else {
            $content['message']     = trans('movile::messages.subscription.failed');
            $content['transported'] = $arrayResponse;
            $this->pushError($content);
        }
    }

    public function isSuccess(): bool
    {
        $arrayResponse = $this->originalResponse->toArray();
        return data_get($arrayResponse, 'subscription_id') && data_get($arrayResponse, 'account_id');
    }
}
