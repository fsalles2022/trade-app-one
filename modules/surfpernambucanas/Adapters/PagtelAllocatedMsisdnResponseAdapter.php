<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use TradeAppOne\Domain\HttpClients\Responseable;

class PagtelAllocatedMsisdnResponseAdapter extends PagtelResponseAdapter
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);

        $this->adapted = array_merge(
            $this->adaptMsisdnData(),
            $this->adapted
        );
    }

    /** @return mixed[] */
    protected function adaptMsisdnData(): array
    {
        return [
            'msisdn' => $this->originalResponse->get('msisdn'),
        ];
    }
}
