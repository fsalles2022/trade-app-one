<?php

namespace TimBR\Models;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBRExpress extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'eligibilityToken',
                'msisdn',
                'portedNumber',
                'iccid',
                'creditCard',
                'areaCode',
                'productName',
                'package',
                'automaticPackages',
                'promoter',
                'selectedServices',
                'timProtocolSearchTries'
            ]
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'mode'                  => Rule::in(['MIGRATION', 'ACTIVATION', 'PORTABILITY']),
            'msisdn'                => 'required_if:mode,MIGRATION',
            'portedNumber'          => 'required_if:mode,PORTABILITY',
            'areaCode'              => 'sometimes|digits:2',
            'eligibilityToken'      => 'sometimes|required',
            'iccid'                 => 'required_if:mode,ACTIVATION',
            'creditCard.token'      => 'required|alpha_dash',
            'creditCard.cvv'        => 'required|digits_between:3,5',
            'customer.firstName'    => 'required|string',
            'customer.lastName'     => 'required|string',
            'customer.cpf'          => 'required|cpf',
            'customer.birthday'     => 'required|date',
            'customer.zipCode'      => 'required',
            'customer.number'       => 'required|numeric',
            'customer.neighborhood' => 'required',
            'customer.rgDate'       => 'required| date_format:"' . Formats::DATE . '"',
            'customer.rgState'      => 'required',
            'customer.rgLocal'      => 'required',
            'customer.localId'      => 'required|string',
            'customer.local'        => 'required',
            'loyalty'               => 'sometimes',
            'label'                 => 'required',
            'loyalty.price'         => 'sometimes',
            'loyalty.id'            => 'sometimes',
            'promoter'              => 'sometimes|array',
            'promoter.name'         => 'sometimes|string',
            'promoter.cpf'          => 'sometimes|cpf',
            'selectedServices'      => 'sometimes|array',
            'timProtocolSearchTries'=> 'sometimes|numeric'
        ];
    }
}
