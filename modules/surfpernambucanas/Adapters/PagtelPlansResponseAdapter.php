<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\HttpClients\Responseable;

class PagtelPlansResponseAdapter extends PagtelResponseAdapter
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);

        $this->adapted = array_merge(
            $this->adaptPlansData(),
            $this->adapted
        );
    }

    /** @return mixed[] */
    protected function adaptPlansData(): array
    {
        $plans = $this->originalResponse->get('valueList', []);

        $plans = Collect($plans)->map(function (array $plan): array {
            return [
                'price' => MoneyHelper::formatCentsToReal((int) data_get($plan, 'value', 0)),
                'label' => data_get($plan, 'note'),
            ];
        });

        return [
            'plans' => $plans->toArray(),
        ];
    }
}
