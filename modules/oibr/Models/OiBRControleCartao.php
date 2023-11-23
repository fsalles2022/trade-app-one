<?php

namespace OiBR\Models;

use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class OiBRControleCartao extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            ['msisdn', 'token', 'cvv', 'valueAdhesion', 'iccid', 'areaCode']
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'msisdn'            => 'required_without:iccid|digits:11',
            'iccid'             => 'required_without:msisdn|digits:19',
            'token'             => 'required',
            'cvv'               => 'required',
            'customer.birthday' => 'required|date_format:"' . Formats::DATE . '"'
        ];
    }
}
