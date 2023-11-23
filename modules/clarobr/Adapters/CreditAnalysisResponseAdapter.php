<?php

namespace ClaroBR\Adapters;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class CreditAnalysisResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $restResponse)
    {
        $arrayResponse = $restResponse->toArray();
        try {
            if ($arrayResponse['data']['credit'] == 0) {
                $content['message'] = trans('siv::messages.score.no_score');
                $this->pushError($content, Response::HTTP_UNPROCESSABLE_ENTITY);
            } elseif ($arrayResponse['type'] == 'error') {
                $this->status = Response::HTTP_PRECONDITION_REQUIRED;
            } else {
                $this->adapted = $arrayResponse;
            }
        } catch (\ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }
}
