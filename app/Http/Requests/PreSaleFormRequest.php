<?php

namespace TradeAppOne\Http\Requests;

class PreSaleFormRequest extends FormRequestAbstract
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'serviceTransaction' => 'required|string',
            'imei' => 'required|string|max:15'
        ];
    }
}
