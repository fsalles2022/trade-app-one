<?php

namespace TimBR\Models;

use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBRPrePago extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), [
            'msisdn',
            'iccid',
            'mode',
            'areaCode',
            'portedNumber',
            'eligibilityToken',
            'productName',
            'promoter',
            'automaticPackages',
            'timProtocolSearchTries'
        ]);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'label'                 => 'required',
            'mode'                  => 'required',
            'iccid'                 => 'required_if:mode,ACTIVATION',
            'eligibilityToken'      => 'sometimes',
            'msisdn'                => 'required_if:mode,MIGRATION',
            'portedNumber'          => 'required_if:mode,PORTABILITY',
            'customer.cpf'          => 'required|cpf',
            'customer.firstName'    => 'required|string',
            'customer.lastName'     => 'required|string',
            'customer.birthday'     => 'required| date_format:"' . Formats::DATE . '"',
            'customer.zipCode'      => 'required',
            'customer.number'       => 'sometimes|numeric',
            'customer.neighborhood' => 'sometimes',
            'customer.rgDate'       => 'sometimes| date_format:"' . Formats::DATE . '"',
            'customer.rgState'      => 'sometimes',
            'customer.localId'      => 'sometimes|string',
            'customer.local'        => 'sometimes',
            'offer'                 => 'sometimes',
            'promoter'              => 'sometimes|array',
            'promoter.name'         => 'sometimes|string',
            'promoter.cpf'          => 'sometimes|cpf',
            'timProtocolSearchTries'=> 'sometimes|numeric'
        ];
    }
}
