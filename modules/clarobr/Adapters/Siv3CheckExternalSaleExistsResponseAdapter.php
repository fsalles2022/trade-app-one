<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class Siv3CheckExternalSaleExistsResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        $this->status = $originalResponse->getStatus();
        $this->adapterResponse();
    }

    protected function adapterResponse(): void
    {
        $saleExists    = $this->originalResponse->get('saleExists', false);
        $this->adapted = [
            'saleExists' => $saleExists,
            'saleId' => $this->originalResponse->get('saleId', 0)
        ];

        if ($this->originalResponse->getStatus() !== Response::HTTP_OK || $saleExists) {
            $this->status  = Response::HTTP_PRECONDITION_FAILED;
            $this->adapted = trans('siv::messages.activation.check_sale_failed');
        }
    }
}
