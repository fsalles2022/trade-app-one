<?php

namespace NextelBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class ValidationBankDataFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bankId'    => 'required|string',
            'agency'    => 'required|string',
            'account'   => 'required|string',
            'operation' => 'sometimes|string'
        ];
    }
}
