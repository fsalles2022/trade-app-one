<?php


namespace Generali\Models;

use TradeAppOne\Domain\Models\Collections\Service;

class Generali extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            $this->getFillable(),
            [
                'premium'
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'premium.total'          => 'required|numeric',
            'premium.validity.start' => 'required|string',
            'premium.validity.end'   => 'required|string',
        ];
    }
}
