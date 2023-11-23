<?php

declare(strict_types=1);

namespace TimBR\Adapters;

use ErrorException;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimSimCardActivationResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $response)
    {
        parent::__construct($response);
        $arrayResponse = $response->toArray();
        $content       = [];

        try {
            if (((int) data_get($arrayResponse, 'transactionStatus', -1)) !== 2) {
                $originalMessage    = data_get($arrayResponse, 'transactionStatusControl');
                $content['message'] = $originalMessage;
                $this->pushError($content);

                return;
            }

            $this->adapted = [
                'message' => trans('timBR::messages.sim_card_activation.success'),
                'data' => $arrayResponse,
            ];
        } catch (ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }
}
