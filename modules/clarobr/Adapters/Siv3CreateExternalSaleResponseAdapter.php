<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class Siv3CreateExternalSaleResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        $this->adapterResponse();
        $this->status = $originalResponse->getStatus();
    }

    protected function adapterResponse(): void
    {
        $this->adapted = [
            'success' => (bool) $this->originalResponse->get('success', false),
            'saleId' => $this->originalResponse->get('saleId', 0)
        ];

        if ($this->originalResponse->getStatus() !== Response::HTTP_CREATED) {
            $this->adapted = trans('siv::messages.activation.save_sale_failed');
        }
    }
}
