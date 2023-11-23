<?php

namespace VivoBR\Models;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Models\Collections\Service;

class VivoControleCartao extends Service
{
    public function fill(array $attributes): VivoControleCartao
    {
        $this->fillable = array_merge(
            $this->getFillable(),
            [
                'msisdn',
                'iccid',
                'portedNumber',
                'mode',
                'invoiceType',
                'areaCode',
                'biometrics',
                'planSlug'
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'product'            => 'required',
            'invoiceType'        => ['required', Rule::in(['EMAIL', 'CARTAO_CREDITO', 'VIA_POSTAL'])],
            'areaCode'           => 'sometimes',
            'iccid'              => 'digits:20',
            'customer.firstName' => 'required',
            'customer.lastName'  => 'required',
            'customer.cpf'       => 'required|cpf',
        ];
    }
}
