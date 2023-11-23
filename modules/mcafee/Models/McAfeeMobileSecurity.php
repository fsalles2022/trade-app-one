<?php

namespace McAfee\Models;

use TradeAppOne\Domain\Models\Collections\Service;

class McAfeeMobileSecurity extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'quantity',
                'gatewayReference',
                'mcAfeeReference',
                'mcAfeeActivationCode',
                'productKey'
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'customer.cpf'       => 'required|cpf',
            'customer.firstName' => 'required|string',
            'customer.lastName'  => 'required|string',
            'customer.email'     => 'required|email',
            'customer.mainPhone' => 'required',
            'customer.password'  => 'string'
        ];
    }
}
