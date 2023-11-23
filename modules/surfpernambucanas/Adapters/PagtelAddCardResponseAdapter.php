<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use TradeAppOne\Domain\HttpClients\Responseable;

class PagtelAddCardResponseAdapter extends PagtelResponseAdapter
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);

        $this->adapted = array_merge(
            $this->adaptPaymentData(),
            $this->adapted
        );
    }

    /** @return mixed[] */
    protected function adaptPaymentData(): array
    {
        return [
            'paymentId' => $this->originalResponse->get('paymentID'),
        ];
    }
}
