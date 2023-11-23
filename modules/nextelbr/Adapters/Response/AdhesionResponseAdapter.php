<?php

namespace NextelBR\Adapters\Response;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class AdhesionResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        $arrayResonse = $originalResponse->toArray();
        if (data_get($arrayResonse, 'nextelIDExterno')) {
            $this->adapted['message']         = trans('nextelBR::messages.activation.success');
            $this->adapted['nextelIDExterno'] = data_get($arrayResonse, 'nextelIDExterno');
        } else {
            $content['message'] = data_get($arrayResonse, 'mensagem');
            $this->pushError($content);
        }
    }

    public function isSuccess(): bool
    {
        return filled(data_get($this->originalResponse->toArray(), 'nextelIDExterno'));
    }
}
