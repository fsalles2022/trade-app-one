<?php

namespace Reports\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class ReportsFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'endDate'        => 'sometimes|nullable|date',
            'networks'       => 'array|exists:networks,slug',
            'pointsOfSale'   => 'array',
            'startDate'      => 'sometimes|nullable|date',
            'amount'         => 'numeric'
        ];
    }
}
