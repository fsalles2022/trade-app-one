<?php

namespace OiBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class OiBRREgisterCreditCardFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pan'   => 'required',
            'month' => 'required|numeric',
            'year'  => 'required|numeric',
        ];
    }
}
