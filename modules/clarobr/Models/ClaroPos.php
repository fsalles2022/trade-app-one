<?php


namespace ClaroBR\Models;

use ClaroBR\ClaroModels;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Models\Collections\Service;

class ClaroPos extends Service implements ClaroModels
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'dueDate',
                'invoiceType',
                'areaCode',
                'iccid',
                'msisdn',
                'promotion',
                'device',
                'promotionLabel',
                'from',
                'portedNumber',
                'checkingAccount',
                'bankId',
                'agency',
                'isPreSale',
                'dependents',
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'mode'                           => 'required',
            'isPreSale'                      => 'boolean',
            'portedNumber'                   => 'sometimes|required_without:areaCode',
            'dueDate'                        => 'required',
            'msisdn'                         => 'sometimes|required',
            'areaCode'                       => 'sometimes|required',
            'promotion'                      => 'required',
            'portedNumber'                   => 'sometimes',
            'invoiceType'                    => [
                'required',
                Rule::in([
                    'EMAIL',
                    'VIA_POSTAL',
                    'DEBITO_AUTOMATICO'
                ])
            ],
            'bankId'                         => 'required_if:invoiceType, DEBITO_AUTOMATICO',
            'agency'                         => 'required_if:invoiceType, DEBITO_AUTOMATICO',
            'checkingAccount'                => 'required_if:invoiceType, DEBITO_AUTOMATICO',
            'imei'                           => 'digits:15',
            'device'                         => 'sometimes|required',
            'customer.firstName'             => 'required',
            'customer.lastName'              => 'required',
            'customer.email'                 => 'required|email',
            'customer.cpf'                   => 'required|cpf',
            'customer.gender'                => ['required', Rule::in(['M', 'F'])],
            'customer.birthday'              => 'required',
            'customer.filiation'             => 'required',
            'customer.mainPhone'             => 'required',
            'customer.secondaryPhone'        => 'required',
            'customer.rg'                    => 'required',
            'customer.rgLocal'               => 'required',
            'customer.number'                => 'required',
            'customer.zipCode'               => 'required',
            'customer.neighborhood'          => 'required',
            'customer.localId'               => 'required',
            'customer.local'                 => 'required',
            'customer.city'                  => 'required',
            'customer.state'                 => 'required',
            'dependents'                     => 'sometimes|required',
            'dependents.*.mode'              => 'required_with:dependents',
            'dependents.*.type'              => 'required_with:dependents',
            'dependents.*.promotion.product' => 'required_with:dependents|numeric',
            'dependents.*.iccid'             => 'sometimes|required_with:dependents.*.portedNumber|digits:20',
            'dependents.*.msisdn'            => 'required_without:dependents.*.iccid',
            'dependents.*.portedNumber'      => 'sometimes|required',
        ];
    }
}
