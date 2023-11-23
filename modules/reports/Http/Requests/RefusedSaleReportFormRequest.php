<?php

namespace Reports\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class RefusedSaleReportFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'startDate' => 'sometimes|date',
            'endDate' => 'sometimes|date',
            'clientName' => 'sometimes|string',
            'clientCpf' => 'sometimes|max:11',
        ];
    }
}
