<?php

namespace ClaroBR\Models;

use ClaroBR\ClaroModels;
use TradeAppOne\Domain\Models\Collections\Service;

class ClaroPre extends Service implements ClaroModels
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'imei',
                'iccid',
                'msisdn',
                'portedNumber',
                'promotion',
                'areaCode',
                'chipCombo',
                'isPreSale'
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'promotion'             => 'required|sometimes',
            'isPreSale'             => 'boolean',
            'iccid'                 => 'required_without:msisdn|digits:20',
            'areaCode'              => 'required|sometimes',
            'msisdn'                => 'required|sometimes|digits_between:10,11',
            'imei'                  => 'digits:15',
            'portedNumber'          => 'sometimes|required',
            'chipCombo'             => 'sometimes|required',
            'customer.firstName'    => 'required',
            'customer.lastName'     => 'required',
            'customer.email'        => 'required|email',
            'customer.cpf'          => 'required|cpf',
            'customer.birthday'     => 'required',
            'customer.mainPhone'    => 'required',
            'customer.zipCode'      => 'required',
            'customer.neighborhood' => 'required',
            'customer.localId'      => 'required',
            'customer.local'        => 'required',
            'customer.city'         => 'required',
            'customer.state'        => 'required',
        ];
    }
}
