<?php

namespace ClaroBR\Models;

use ClaroBR\ClaroModels;
use ClaroBR\Services\SivSaleAssistance;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class ControleBoleto extends Service implements ClaroModels
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'iccid',
                'areaCode',
                'msisdn',
                'dueDate',
                'invoiceType',
                'portedNumber',
                'bankId',
                'agency',
                'promotion',
                'isPreSale',
                'promotionLabel',
                'checkingAccount',
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'iccid'                   => 'digits:20',
            'isPreSale'               => 'boolean',
            'mode'                    => 'required',
            'msisdn'                  => 'digits:11',
            'promotion'               => 'required',
            'portedNumber'            => 'sometimes',
            'invoiceType'             => [
                'required',
                Rule::in([
                    'EMAIL',
                    'VIA_POSTAL',
                    'DEBITO_AUTOMATICO'
                ])
            ],
            'dueDate'                 => 'required',
            'bankId'                  => 'required_if:invoiceType, DEBITO_AUTOMATICO',
            'agency'                  => 'required_if:invoiceType, DEBITO_AUTOMATICO',
            'checkingAccount'         => 'required_if:invoiceType, DEBITO_AUTOMATICO',
            'customer.firstName'      => 'required',
            'customer.lastName'       => 'required',
            'customer.email'          => 'required|email',
            'customer.cpf'            => 'required|cpf',
            'customer.gender'         => ['required', Rule::in(['M', 'F'])],
            'customer.birthday'       => 'required|date_format:"' . Formats::DATE . '"',
            'customer.filiation'      => 'required',
            'customer.mainPhone'      => 'required',
            'customer.secondaryPhone' => 'required',
            'customer.complement'     => 'sometimes',
            'customer.rg'             => 'required',
            'customer.rgLocal'        => 'required',
            'customer.number'         => 'required',
            'customer.zipCode'        => 'required',
            'customer.neighborhood'   => 'required',
            'customer.localId'        => 'required',
            'customer.local'          => 'required',
            'customer.city'           => 'required',
            'customer.state'          => 'required',
        ];
    }

    public function getAssistant()
    {
        return SivSaleAssistance::class;
    }
}
