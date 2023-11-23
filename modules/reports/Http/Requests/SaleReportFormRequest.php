<?php

namespace Reports\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class SaleReportFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serviceTransaction' => 'sometimes|string',
            'startDate'          => 'sometimes|date',
            'endDate'            => 'sometimes|date',
            'cpfSalesman'        => 'sometimes|string',
            'pointOfSaleCnpj'    => 'sometimes|array',
            'pointOfSaleSlug'    => 'sometimes|array',
            'page'               => 'sometimes|numeric'
        ];
    }
}
