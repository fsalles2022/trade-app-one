<?php

namespace ClaroBR\Models;

use ClaroBR\ClaroModels;
use ClaroBR\Services\SivSaleAssistance;
use TradeAppOne\Domain\Models\Collections\Service;

class ControleFacil extends Service implements ClaroModels
{
    public const ASSISTANCE = SivSaleAssistance::class;

    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), ['msisdn', 'iccid', 'isPreSale', 'portedNumber']);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'mode'                 => 'required',
            'isPreSale'            => 'boolean',
            'remoteSale'           => 'sometimes|boolean',
            'iccid'                => 'digits:20',
            'msisdn'               => 'digits:11',
            'customer.firstName'   => 'required',
            'customer.lastName'    => 'required',
            'customer.cpf'         => 'required|cpf',
            'integratorPaymentURL' => 'sometimes|string',
            'paymentUrl'           => 'sometimes|string',
            'portedNumber'         => 'digits:11',
            'portedNumberToken'    => 'sometimes|required',
        ];
    }
}
