<?php

namespace OiBR\Adapters;

use Illuminate\Http\Response;
use OiBR\Enumerators\OiBRBusinessCodes;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class OiBRResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $response)
    {
        $arrayResponse = $response->toArray();
        $this->status  = $response->getStatus();
        try {
            if ($response->getStatus() == Response::HTTP_CREATED || $response->getStatus() == Response::HTTP_OK) {
                $content['message'] = trans('messages.default_success');
                $this->adapted      = $content;
            } else {
                if (data_get($arrayResponse, 'message')) {
                    $content['message'] = data_get($arrayResponse, 'message');
                    $this->pushError($content);
                } else {
                    $message = data_get($arrayResponse, 'erros.0.mensagem');
                    $this->replaceMessageBasedOnBussinessCode($message);
                }
            }
        } catch (\ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }

    private function replaceMessageBasedOnBussinessCode($message)
    {
        $bussinessCode = $this->getConstantOfMessage(OiBRBusinessCodes::class, $message);

        if ($bussinessCode) {
            $translationKey     = 'oiBR::messages.' . $bussinessCode;
            $content['message'] = trans($translationKey);
        } else {
            $content['message'] = $message;
        }

        $this->pushError($content, Response::HTTP_NOT_ACCEPTABLE);
    }

    private function getConstantOfMessage($class, $message): ?string
    {
        $refl           = new \ReflectionClass($class);
        $listOfmessages = $refl->getConstants();

        $key = array_search($message, $listOfmessages);

        return $key ? $key : null;
    }

    public static function build($data, $status)
    {
        return response()->json($data, $status);
    }
}
