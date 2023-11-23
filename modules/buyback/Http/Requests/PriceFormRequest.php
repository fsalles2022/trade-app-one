<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class PriceFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'deviceId'           => 'required|numeric',
            'questions'          => 'required',
            'questions.*.id'     => 'required|numeric',
            'questions.*.answer' => 'required'
        ];
    }
}
