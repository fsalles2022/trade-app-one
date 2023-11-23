<?php

namespace TradeAppOne\Http\Requests;

class BackOfficeFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serviceTransaction' => 'required|string',
            'comment'            => 'required|string'
        ];
    }
}
