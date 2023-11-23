<?php

namespace VivoBR\Models;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Models\Collections\Service;
use VivoBR\Enumerators\VivoInvoiceType;

class VivoPosPago extends Service
{
    public const INVOICE_TYPES = [VivoInvoiceType::EMAIL, VivoInvoiceType::CARTAO_CREDITO, VivoInvoiceType::VIA_POSTAL, VivoInvoiceType::DEBITO_AUTOMATICO];

    public function fill(array $attributes): Service
    {
        $this->fillable = array_merge($this->getFillable(), [
            'msisdn',
            'iccid',
            'areaCode',
            'portedNumber',
            'mode',
            'invoiceType',
            'dueDate',
            'fromOperator',
            'biometrics',
            'checkingAccount',
            'bankId',
            'agency',
            'accountType',
        ]);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'product' => 'required',
            'mode' => 'required',
            'iccid' => 'digits:20',
            'msisdn' => 'digits:11',
            'portedNumber' => 'required_with:fromOperator',
            'fromOperator' => 'required_with:portedNumber|between:1,6',
            'invoiceType' => ['required', Rule::in(self::INVOICE_TYPES)],
            'bankId' => 'required_if:invoiceType, DEBITO_AUTOMATICO|numeric|between:1,15',
            'agency' => 'required_if:invoiceType, DEBITO_AUTOMATICO|numeric',
            'checkingAccount' => 'required_if:invoiceType, DEBITO_AUTOMATICO|numeric',
            'accountType' => 'required_if:invoiceType, DEBITO_AUTOMATICO|numeric',
            'dueDate' => 'required_if:invoiceType,EMAIL,VIA_POSTAL|integer|between:1,31',
            'customer.cpf' => 'required|cpf',
            'customer.firstName' => 'required',
            'customer.lastName' => 'required',
            'customer.email' => 'required|email',
            'customer.gender' => ['required', Rule::in(['M', 'F'])],
            'customer.birthday' => 'required',
            'customer.filiation' => 'required',
            'customer.mainPhone' => 'required',
            'customer.secondaryPhone' => 'required',
            'customer.number' => 'required',
            'customer.zipCode' => 'required',
            'customer.neighborhood' => 'required',
            'customer.local' => 'required',
            'customer.city' => 'required',
            'customer.state' => 'required|states_br',
        ];
    }
}
