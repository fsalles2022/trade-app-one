<?php

declare(strict_types=1);

namespace TimBR\Models;

use Illuminate\Validation\Rule;
use TimBR\Enumerators\TimBRInvoiceTypes;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBRBlackExpress extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), [
            'msisdn',
            'iccid',
            'invoiceType',
            'billType',
            'creditCard',
            'mode',
            'areaCode',
            'portedNumber',
            'dueDate',
            'eligibilityToken',
            'loyalty',
            'productName',
            'package',
            'automaticPackages',
            'promoter',
            'selectedServices',
            'timProtocolSearchTries',
            'authenticate',
            'sentToTimCommissioning',
        ]);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'label'                       => 'required',
            'mode'                        => 'required',
            'iccid'                       => 'required_if:mode,ACTIVATION',
            'eligibilityToken'            => 'sometimes',
            'invoiceType'                 => ['required', 'string', Rule::in([TimBRInvoiceTypes::CREDIT_CARD])],
            'billType'                    => ['required', 'string', Rule::in(['Conta Online','Resumida'])],
            'dueDate'                     => 'required',
            'msisdn'                      => 'required_if:mode,MIGRATION',
            'portedNumber'                => 'required_if:mode,PORTABILITY',
            'customer.cpf'                => 'required|cpf',
            'customer.firstName'          => 'required|string',
            'customer.lastName'           => 'required|string',
            'customer.birthday'           => 'required| date_format:"' . Formats::DATE . '"',
            'customer.zipCode'            => 'required',
            'customer.number'             => 'required|numeric',
            'customer.neighborhood'       => 'required',
            'customer.rgDate'             => 'required| date_format:"' . Formats::DATE . '"',
            'customer.rgState'            => 'required',
            'customer.localId'            => 'required|string',
            'customer.local'              => 'required',
            'loyalty'                     => 'sometimes',
            'loyalty.price'               => 'required_with:loyalty',
            'loyalty.id'                  => 'required_with:loyalty',
            'creditCard.token'            => 'required_if:invoiceType,' . TimBRInvoiceTypes::CREDIT_CARD .'|alpha_dash',
            'creditCard.cvv'              => 'required_if:invoiceType,' . TimBRInvoiceTypes::CREDIT_CARD .'|digits_between:3,5',
            'authenticate.linkId'         => 'required|numeric',
            'authenticate.linkUrl'        => 'required|string',
            'promoter'                    => 'sometimes|array',
            'promoter.name'               => 'sometimes|string',
            'promoter.cpf'                => 'sometimes|cpf',
            'selectedServices'            => 'sometimes|array',
            'timProtocolSearchTries'      => 'sometimes|numeric',
        ];
    }
}
