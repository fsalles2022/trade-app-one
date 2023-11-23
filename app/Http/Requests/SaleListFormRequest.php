<?php

namespace TradeAppOne\Http\Requests;

class SaleListFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'sometimes|string',
            'cpf'          => 'sometimes|string',
            'cpfSalesman'  => 'sometimes|string',
            'cpfCustomer'  => 'sometimes|string',
            'saleId'       => 'sometimes|string',
            'operator'     => 'sometimes',
            'imei'         => 'sometimes|numeric|digits:15',
            'log'          => 'sometimes|string',
            'pointsOfSale' => 'sometimes|array',
            'startDate'    => 'sometimes|date',
            'endDate'      => 'sometimes|date',
            'status'       => 'sometimes|array',
            'operation'    => 'sometimes|string',
            'mode'         => 'sometimes|string',
            'networks'     => 'sometimes|array',
            'ntc'          => 'sometimes|string'
        ];
    }
}
