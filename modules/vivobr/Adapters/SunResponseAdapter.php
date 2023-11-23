<?php

namespace VivoBR\Adapters;

use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class SunResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $response)
    {
        $arrayResponse = $response->toArray();
        try {
            if ($arrayResponse['codigo'] == 0) {
                unset($arrayResponse['codigo']);
                $data['data']  = $arrayResponse;
                $this->adapted = $data;
            } else {
                $this->status       = $this->defaultErrorStatus;
                $content['message'] = data_get($arrayResponse, 'mensagem');
                if ($details = data_get($arrayResponse, 'detalhes.0')) {
                    $content['message'] = "{$details}";
                }
                $this->pushError($content);
            }
        } catch (\ErrorException $exception) {
            Log::alert('format-sun-error: ' . $exception->getMessage(), ['response' => $arrayResponse]);
            $this->adapted = $arrayResponse;
        }
    }
}
