<?php

namespace McAfee\Adapters\Response;

use McAfee\Enumerators\McAfeeStatusCode;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;

class McAfeeCancelSubscriptionResponseAdapter extends ResponseAdapterAbstract
{
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
        $this->adapted  = data_get($this->response, 'DATA.RESPONSECONTEXT.ORDER');
    }

    public function isSuccess(): bool
    {
        $returnCode = data_get($this->response, 'DATA.RESPONSECONTEXT.RETURNCODE');
        return $returnCode == McAfeeStatusCode::TRANSACTION_SUCCESS;
    }

    public function getAdapted(): array
    {
        if ($this->isSuccess()) {
            return $this->adapted;
        } else {
            $content['message'] = trans('mcAfee::messages.subscription.invalid_data');
            return $this->pushError($content);
        }
    }
}
