<?php

declare(strict_types=1);

namespace TimBR\Models;

use Illuminate\Validation\Rule;
use TimBR\Enumerators\TimBRInvoiceTypes;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBRBlackMultiDependent extends Service
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
            'masterNumber',
            'dueDate',
            'eligibilityToken',
            'loyalty',
            'productName',
            'directDebit',
            'package',
            'automaticPackages',
            'selectedPackages',
            'promoter',
            'selectedServices',
            'timProtocolSearchTries',
            'authenticate',
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
            'msisdn'                      => 'required_if:mode,MIGRATION',
            'portedNumber'                => 'required_if:mode,PORTABILITY',
            'masterNumber'                => 'required',
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
