<?php

namespace OiBR\Models;

use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class OiBRControleBoleto extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), ['msisdn', 'valueAdhesion']);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'msisdn'            => 'required|digits:11',
            'customer.cpf'      => 'required',
            'customer.zipCode'  => 'required|digits:8',
            'customer.birthday' => 'required|date_format:"' . Formats::DATE . '"',
        ];
    }
}
