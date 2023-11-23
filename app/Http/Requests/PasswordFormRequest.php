<?php

namespace TradeAppOne\Http\Requests;

class PasswordFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['password' => 'required'];
    }
}
