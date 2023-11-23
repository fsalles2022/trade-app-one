<?php

namespace NextelBR\Adapters\Response;

use Illuminate\Support\Facades\Lang;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class PreAdhesionResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        $arrayResonse = $originalResponse->toArray();
        if (! $this->isSuccess()) {
            $content['message'] = data_get($arrayResonse, 'mensagem');
            $this->pushError($content);
        } else {
            $this->adapted = $arrayResonse;
        }
    }

    public function isSuccess(): bool
    {
        return data_get($this->originalResponse->toArray(), 'resultadoCrivo') == 'APROVADO';
    }
}
