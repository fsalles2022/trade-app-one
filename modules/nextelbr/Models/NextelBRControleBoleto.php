<?php

namespace NextelBR\Models;

use Illuminate\Validation\Rule;
use NextelBR\Enumerators\NextelInvoiceTypes;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Service;

class NextelBRControleBoleto extends Service
{
    const CAIXA_ECONOMICA_FEDERAL = '104';

    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            ['iccid', 'areaCode', 'directDebit', 'portedNumber', 'portability', 'invoiceType', 'dueDate', 'offer']
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'mode'                             => Rule::in([Modes::ACTIVATION, Modes::PORTABILITY]),
            'areaCode'                         => 'required|digits:2',
            'dueDate'                          => 'required',
            'iccid'                            => 'required',
            'invoiceType'                      => Rule::in([
                NextelInvoiceTypes::BOLETO,
                NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST
            ]),
            'operatorIdentifiers.numeroPedido' => 'required',
            'operatorIdentifiers.protocolo'    => 'required',
            'customer.score'                   => 'required',
            'portedNumber'                     => 'required_if:mode,' . Modes::PORTABILITY,
            'portability.portabilityDate'      => 'required_if:mode,' . Modes::PORTABILITY . '|after:' . date('Y-m-d')
                . '|date_format:"' . Formats::DATE,
            'portability.fromOperator'         => 'required_if:mode,' . Modes::PORTABILITY,
            'portability.fromOperatorId'       => 'required_if:mode,' . Modes::PORTABILITY,
            'directDebit'                      => 'required_if:invoiceType,DEBITO_AUTOMATICO',
            'directDebit.agency'               => 'required_if:invoiceType,DEBITO_AUTOMATICO',
            'directDebit.checkingAccount'      => 'required_if:invoiceType,DEBITO_AUTOMATICO',
            'directDebit.checkingAccountDv'    => 'required_if:invoiceType,DEBITO_AUTOMATICO',
            'directDebit.bankId'               => 'required_if:invoiceType,DEBITO_AUTOMATICO',
            'directDebit.bankOperation'        => 'required_if:directDebit.bankId,' . self::CAIXA_ECONOMICA_FEDERAL . '|numeric'
        ];
    }
}
