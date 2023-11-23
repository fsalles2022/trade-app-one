<?php

namespace OiBR\Adapters;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class OiBRControleCartaoEligibilityResponseAdapter extends ResponseAdapterAbstract
{
    const AVAILABLE   = 'AVAILABLE';
    const UNAVAILABLE = 'UNAVAILABLE';
    const ACTIVE      = 'ACTIVE';
    const INACTIVE    = 'INACTIVE';

    public function __construct(RestResponse $response)
    {
        $arrayResponse = $response->toArray();
        $this->status  = $response->getStatus();
        try {
            if ($arrayResponse['result']['status'] == self::AVAILABLE) {
                $content['message'] = trans('oiBR::messages.controle_cartao.eligibility.available');
            }
            if ($arrayResponse['result']['status'] == self::ACTIVE) {
                $content['message'] = trans('oiBR::messages.controle_cartao.eligibility.active');
                $this->status       = Response::HTTP_PRECONDITION_FAILED;
            }
            if ($arrayResponse['result']['status'] == self::UNAVAILABLE) {
                $content['message'] = trans('oiBR::messages.controle_cartao.eligibility.unavailable');
                $this->status       = Response::HTTP_PRECONDITION_FAILED;
            }
            if ($arrayResponse['result']['status'] == self::INACTIVE) {
                $content['message'] = trans('oiBR::messages.controle_cartao.eligibility.inactive');
                $this->status       = Response::HTTP_PRECONDITION_FAILED;
            }

            $content['status']      = $arrayResponse['result']['status'];
            $content['transported'] = $arrayResponse;
            $this->adapted          = $content;
        } catch (\ErrorException $exception) {
            $this->adapted['transported'] = $arrayResponse;
        }
    }
}
