<?php

namespace McAfee\Adapters\Response;

use McAfee\Enumerators\McAfeeStatusCode;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;

class McAfeeNewSubscriptionResponseAdapter extends ResponseAdapterAbstract
{
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
        if ($this->isSuccess()) {
            $this->adapted = data_get($this->response, 'DATA.RESPONSECONTEXT.ORDER');
        }
    }

    public function getCode()
    {
        return data_get($this->response, 'DATA.RESPONSECONTEXT.RETURNCODE');
    }

    public function isSuccess(): bool
    {
        return $this->getCode() === McAfeeStatusCode::TRANSACTION_SUCCESS
            || $this->getCode() === McAfeeStatusCode::TRANSACTION_SUCCESS_EMAIL_EXISTS
            || $this->getCode() === McAfeeStatusCode::TRANSACTION_SUCCESS_CONTEXT_FOR_ANOTHER_EMAIL;
    }

    public function getAdapted(): array
    {
        $code = data_get($this->response, 'DATA.RESPONSECONTEXT.RETURNCODE');

        if ($code === McAfeeStatusCode::INVALID_DATA) {
            $content['message'] = trans('mcAfee::messages.subscription.invalid_data');
            return $this->pushError($content);
        }

        if ($code === McAfeeStatusCode::TRANSACTION_FAILED) {
            $content['message'] = trans('mcAfee::messages.subscription.transaction_failed');
            return $this->pushError($content);
        }

        return $this->adapted;
    }
}
