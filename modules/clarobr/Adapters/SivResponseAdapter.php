<?php

namespace ClaroBR\Adapters;

use ClaroBR\Exceptions\ResponseEmptyException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class SivResponseAdapter extends ResponseAdapterAbstract
{
    const NUMERO_NAO_ATIVO   = 'Os parâmetros [produto] são obrigatórios';
    const NUMERO_NAO_ATIVO_2 = 'Verifique se as políticas de uso para a operação solicitada';
    const SIM_CARD_INVALID   = 'Simcard inválido.';

    public function __construct(RestResponse $response)
    {
        $arrayResponse          = $response->toArray();
        $this->originalResponse = $response;

        throw_if(empty($arrayResponse), new ResponseEmptyException());
        $this->adapted['errors'] = [];
        try {
            if ($arrayResponse['type'] == 'success') {
                $data['data']       = $arrayResponse['data'];
                $message['message'] = $arrayResponse['message'];
                $this->adapted      = array_merge($message, $data);
            }

            if ($arrayResponse['type'] == 'error' || $response->getStatus() != Response::HTTP_OK) {
                $message = $arrayResponse['message'];
                $status  = Response::HTTP_PRECONDITION_FAILED;

                switch ($message) {
                    case self::NUMERO_NAO_ATIVO:
                        $content['message'] = trans('siv::messages.NUMERO_NAO_ATIVO');
                        break;
                    case self::SIM_CARD_INVALID:
                        $content['message'] = $message;
                        $status             = Response::HTTP_PARTIAL_CONTENT;
                        break;
                    default:
                        $content['message'] = $message;
                }

                $this->pushError($content, $status);
                if (isset($arrayResponse['content'])) {
                    $content['description'] = $arrayResponse['content']['motivo'];
                    if ($arrayResponse['content']['tipo'] == 'ValidarTokenCliente') {
                        $this->pushError($content, Response::HTTP_PRECONDITION_REQUIRED);
                    }
                    if ($arrayResponse['content']['direcionamento'] == self::NUMERO_NAO_ATIVO_2) {
                        $content['message'] = trans('siv::messages.NUMERO_NAO_ATIVO');
                        $this->pushError($content, Response::HTTP_NOT_ACCEPTABLE);
                    }
                }
            }
        } catch (\ErrorException $exception) {
            Log::alert('format-siv-error: ' . $exception->getMessage(), ['response' => $arrayResponse]);
            $this->adapted = $arrayResponse;
        }
    }

    public function isSimCardInvalid(): bool
    {
        $message = data_get($this->getOriginal(), 'message');
        return strpos($message, self::SIM_CARD_INVALID) !== false;
    }
}
