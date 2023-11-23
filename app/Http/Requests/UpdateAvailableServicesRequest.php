<?php

namespace TradeAppOne\Http\Requests;

class UpdateAvailableServicesRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'networkId' => 'required|integer',
            'pointOfSaleId' => 'required|integer',
            'services' => 'required|array',
        ];
    }
}
