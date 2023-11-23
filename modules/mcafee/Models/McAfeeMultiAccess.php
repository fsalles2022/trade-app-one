<?php

namespace McAfee\Models;

use TradeAppOne\Domain\Models\Collections\Service;

class McAfeeMultiAccess extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'license',
                'payment',
                'retryPayment',
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'customer.cpf'       => 'required|cpf',
            'customer.firstName'  => 'required|string|max:20',
            'customer.lastName'  => 'required|string|max:20',
            'customer.email'     => 'required|email',
            'customer.mainPhone' => 'required',
            'customer.password'  => 'required|min:8|max:50',
            'license.quantity'   => 'required|numeric'
        ];
    }
}
