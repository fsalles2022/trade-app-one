<?php

namespace NextelBR\Models;

use Illuminate\Validation\Rule;
use NextelBR\Enumerators\NextelInvoiceTypes;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Service;

class NextelBRControleCartao extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), [
            'iccid',
            'areaCode',
            'portedNumber',
            'portability',
            'invoiceType',
            'offer',
            'dueDate'
        ]);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'mode'                             => Rule::in([Modes::ACTIVATION, Modes::PORTABILITY]),
            'areaCode'                         => 'required|digits:2',
            'iccid'                            => 'required',
            'invoiceType'                      => 'required',
            'operatorIdentifiers.numeroPedido' => 'required',
            'operatorIdentifiers.protocolo'    => 'required',
            'customer.score'                   => 'required',
            'portedNumber'                     => 'required_if:mode,' . Modes::PORTABILITY,
            'portability.portabilityDate'      => 'required_if:mode,' . Modes::PORTABILITY,
            'portability.fromOperator'         => 'required_if:mode,' . Modes::PORTABILITY,
            'portability.fromOperatorId'       => 'required_if:mode,' . Modes::PORTABILITY,
            'invoiceType'                      => Rule::in(NextelInvoiceTypes::CARTAO_DE_CREDITO)
        ];
    }
}
