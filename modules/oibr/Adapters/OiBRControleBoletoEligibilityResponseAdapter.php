<?php

namespace OiBR\Adapters;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class OiBRControleBoletoEligibilityResponseAdapter extends ResponseAdapterAbstract
{
    const DISPONIVEL   = 'DISPONIVEL';
    const INDISPONIVEL = 'INDISPONIVEL';
    const ATIVO        = 'ATIVO';
    const INATIVO      = 'INATIVO';

    public function __construct(RestResponse $response)
    {
        $arrayResponse = $response->toArray();
        $this->status  = $response->getStatus();
        $statusBoleto  = $arrayResponse['boleto'];

        try {
            switch ($statusBoleto) {
                case self::DISPONIVEL:
                    $message = trans('oiBR::messages.controle_boleto.eligibility.disponivel');
                    break;
                case self::INDISPONIVEL:
                    $message      = trans('oiBR::messages.controle_boleto.eligibility.indisponivel');
                    $this->status = Response::HTTP_PRECONDITION_FAILED;
                    break;
                case self::ATIVO:
                    $message      = trans('oiBR::messages.controle_boleto.eligibility.ativo');
                    $this->status = Response::HTTP_PRECONDITION_FAILED;
                    break;
                case self::INATIVO:
                    $message      = trans('oiBR::messages.controle_boleto.eligibility.inativo');
                    $this->status = Response::HTTP_PRECONDITION_FAILED;
                    break;
                default:
                    $this->adapted = $arrayResponse;
            }

            $content['message']     = $message;
            $content['status']      = $statusBoleto;
            $content['transported'] = $arrayResponse;
            $this->adapted          = $content;
        } catch (\ErrorException $exception) {
            $this->adapted['transported'] = $arrayResponse;
        }
    }
}
