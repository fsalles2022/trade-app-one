<?php

namespace VivoBR\Models;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Models\Collections\Service;
use VivoBR\Enumerators\VivoInvoiceType;

class VivoInternetMovelPos extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), [
            'msisdn',
            'iccid',
            'areaCode',
            'portedNumber',
            'mode',
            'invoiceType',
            'dueDate',
            'fromOperator',
            'biometrics'
        ]);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'product'                 => 'required',
            'mode'                    => 'required',
            'iccid'                   => 'digits:20',
            'portedNumber'            => 'required_with:fromOperator',
            'fromOperator'            => 'required_with:portedNumber|between:1,6',
            'invoiceType'             => ['required', Rule::in([VivoInvoiceType::EMAIL, VivoInvoiceType::VIA_POSTAL])],
            'dueDate'                 => 'required_if:invoiceType,' . VivoInvoiceType::EMAIL .',' . VivoInvoiceType::VIA_POSTAL .'|integer|between:1,31',
            'customer.cpf'            => 'required|cpf',
            'customer.firstName'      => 'required',
            'customer.lastName'       => 'required',
            'customer.email'          => 'required|email',
            'customer.gender'         => ['required', Rule::in(['M', 'F'])],
            'customer.birthday'       => 'required',
            'customer.filiation'      => 'required',
            'customer.mainPhone'      => 'required',
            'customer.secondaryPhone' => 'required|different:customer.mainPhone',
            'customer.number'         => 'required',
            'customer.zipCode'        => 'required',
            'customer.neighborhood'   => 'required',
            'customer.local'          => 'required',
            'customer.city'           => 'required',
            'customer.state'          => 'required|states_br',
        ];
    }
}
