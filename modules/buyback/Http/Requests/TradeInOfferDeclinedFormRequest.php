<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class TradeInOfferDeclinedFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cpfSalesman' => 'sometimes',
            'nameCustomer' => 'sometimes'
        ];
    }
}
