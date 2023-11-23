<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class SaleListIntegratorsFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string',
            'cpf' => 'sometimes|string',
            'cpfSalesman' => 'sometimes|string',
            'cpfCustomer' => 'sometimes|string',
            'saleId' => 'sometimes|string',
            'saleTransaction' => 'sometimes|string',
            'operator' => 'sometimes|string',
            'imei' => 'sometimes|numeric|digits:15',
            'log' => 'sometimes|string',
            'pointsOfSale' => 'sometimes|array',
            'startDate' => 'sometimes|date',
            'endDate' => 'sometimes|date',
            'status' => [
                [
                    'sometimes',
                    Rule::in([
                        ServiceStatus::APPROVED,
                        ServiceStatus::SUBMITTED,
                        ServiceStatus::CANCELED
                    ]),
                    'string',
                ],
                [
                    'sometimes',
                    'array'
                ],
            ],
            'status.*' =>
                [
                    'sometimes',
                    Rule::in([
                        ServiceStatus::APPROVED,
                        ServiceStatus::SUBMITTED,
                        ServiceStatus::CANCELED
                    ]),
                    'string',
                ],
            'operation' => 'sometimes|string',
            'mode' => 'sometimes|string',
            'networks' => 'sometimes|array',
        ];
    }
}
