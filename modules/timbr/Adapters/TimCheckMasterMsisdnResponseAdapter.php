<?php

declare(strict_types=1);

namespace TimBR\Adapters;

use ErrorException;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimCheckMasterMsisdnResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $response)
    {
        parent::__construct($response);
        $arrayResponse = $response->toArray();
        $content       = [];

        try {
            if ($response->get('validated') === 'false') {
                $originalMessage    = data_get($arrayResponse, 'reason');
                $content['message'] = $originalMessage;
                $this->pushError($content);

                return;
            }

            $this->adapted = $arrayResponse;
        } catch (ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }
}
