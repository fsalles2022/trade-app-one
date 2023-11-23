<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use TradeAppOne\Domain\HttpClients\Responseable;

class PagtelActivationActivateResponseAdapter extends PagtelActivationResponseAdapter
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);

        $this->adapted = array_merge(
            $this->adaptActivateData(),
            $this->adapted
        );
    }

    /** @return mixed[] */
    protected function adaptActivateData(): array
    {
        $data = $this->originalResponse->get('payload', []);

        return [
            'activationId'  => data_get($data, 'activationId'),
            'iccid'         => data_get($data, 'iccid'),
            'msisdn'        => data_get($data, 'msisdn'),
        ];
    }
}
