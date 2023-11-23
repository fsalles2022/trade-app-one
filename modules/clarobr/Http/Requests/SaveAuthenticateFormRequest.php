<?php

namespace ClaroBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class SaveAuthenticateFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cpf' => 'required|cpf|max:11',
            'serviceTransaction' => 'required|string'
        ];
    }
}
