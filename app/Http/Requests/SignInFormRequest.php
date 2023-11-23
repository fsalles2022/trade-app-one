<?php

namespace TradeAppOne\Http\Requests;

class SignInFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cpf'      => 'required|string',
            'password' => 'required',
        ];
    }
}
