<?php

namespace TimBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class TimBRRegisterCreditCardFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pan'   => 'required',
            'month' => 'required|between:1,12',
            'year'  => 'required|date_format:Y|after_or_equal:'.now()->format('Y'),
        ];
    }
}
