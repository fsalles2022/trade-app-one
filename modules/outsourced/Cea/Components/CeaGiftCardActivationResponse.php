<?php

namespace Outsourced\Cea\Components;

use Outsourced\Cea\Constants\CeaGiftCardStatus;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;

class CeaGiftCardActivationResponse
{
    protected $response;

    public function __construct($response)
    {
        $this->response = ObjectHelper::convertToArray($response);
    }

    public function isActivated(): bool
    {
        return data_get($this->response, 'Status') === CeaGiftCardStatus::ACTIVE;
    }

    public function get(): array
    {
        return $this->response;
    }

    public function getIDTransacao(): ?string
    {
        return data_get($this->response, 'IDTransacao');
    }
}
