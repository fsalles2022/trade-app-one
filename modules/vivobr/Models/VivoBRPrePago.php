<?php

namespace VivoBR\Models;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Models\Collections\Service;

class VivoBRPrePago extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'msisdn',
                'iccid',
                'areaCode',
                'portedNumber',
                'mode',
                'invoiceType',
                'dueDate',
                'fromOperator',
                'biometrics',
                'preActivation',
                'rechargeValue'
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'iccid'                          => 'required_if:mode,ACTIVATION',
            'fromOperator'                   => 'required_with:portedNumber',
            'portedNumber'                   => 'required_if:mode,PORTABILITY',
            'customer.cpf'                   => 'required|cpf',
            'customer.firstName'             => 'required',
            'customer.lastName'              => 'required'
        ];
    }
}
