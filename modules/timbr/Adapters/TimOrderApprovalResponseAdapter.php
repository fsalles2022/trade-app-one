<?php

declare(strict_types=1);

namespace TimBR\Adapters;

use ErrorException;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimOrderApprovalResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $response)
    {
        parent::__construct($response);
        $arrayResponse = $response->toArray();
        $content       = [];

        try {
            if ($this->checkIsDisapprovalCreditAnalysisResponse($response)) {
                $originalMessage    = data_get($arrayResponse, 'reason.description');
                $content['message'] = $originalMessage;
                $this->pushError($content);

                return;
            }

            $this->adapted = $arrayResponse;
        } catch (ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }

    private function checkIsDisapprovalCreditAnalysisResponse(RestResponse $response): bool
    {
        $timStatus     = mb_strtoupper((string) data_get($response->toArray(), 'reason.status'));
        $timReasonCode = (int) data_get($response->toArray(), 'reason.reasonCode');

        if ($timStatus === 'NOK' || ($timStatus === 'OK' && $timReasonCode < 0)) {
            return true;
        }

        return false;
    }
}
