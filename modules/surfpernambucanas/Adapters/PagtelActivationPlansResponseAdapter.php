<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\HttpClients\Responseable;
use Illuminate\Support\Arr;

class PagtelActivationPlansResponseAdapter extends PagtelActivationResponseAdapter
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
        $plans = $this->originalResponse->get('payload', []);

        $plans = collect($plans)->map(function (array $plan): array {
            $advantages = $this->adaptAdvantages(Arr::get($plan, 'advantages', []));

            return [
                'id'                => Arr::get($plan, 'plan_id'),
                'label'             => Arr::get($plan, 'name'),
                'price'             => MoneyHelper::formatCentsToReal((int) Arr::get($plan, 'value', 0)),
                'validity'          => Arr::get($plan, 'validity'),
                'type'              => Arr::get($plan, 'type'),
                'type_description'  => Arr::get($plan, 'type_description'),
                'advantages'        => $advantages,
            ];
        });

        return [
            'plans' => $plans->toArray(),
        ];
    }

    /**
     * @param array[] $advantages
     * @return array[]
     */
    protected function adaptAdvantages(array $advantages): array
    {
        return collect($advantages)
            ->map(function (array $advantage): array {
                return [
                    'label'         => data_get($advantage, 'title'),
                    'description'   => data_get($advantage, 'description'),
                    'alias'         => data_get($advantage, 'alias'),
                ];
            })
            ->toArray();
    }
}
