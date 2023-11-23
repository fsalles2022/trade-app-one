<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportHistoryFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'firstName' => 'sometimes|string',
            'cpf'       => 'sometimes|string'
        ];
    }
}
