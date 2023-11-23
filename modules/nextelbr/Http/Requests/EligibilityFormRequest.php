<?php

namespace NextelBR\Http\Requests;

use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class EligibilityFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'areaCode'           => 'required',
            'customer'           => 'required',
            'customer.firstName' => 'required|' . Formats::NAMES,
            'customer.lastName'  => 'required|' . Formats::NAMES,
            'customer.cpf'       => 'required|cpf',
            'customer.birthday'  => 'required| date_format:"' . Formats::DATE . '"',
            'customer.filiation' => 'required|' . Formats::NAMES,
            'customer.zipCode'   => 'required',
        ];
    }
}
