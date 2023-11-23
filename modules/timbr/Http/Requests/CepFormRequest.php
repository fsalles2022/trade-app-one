<?php

namespace TimBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class CepFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['cep' => 'required|string'];
    }
}
