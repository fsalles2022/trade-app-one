<?php

namespace NextelBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class NextelBRLogM4uFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serviceTransaction' => 'required',
            'status'             => 'required',
            'm4uResponse'        => 'required',
        ];
    }
}
