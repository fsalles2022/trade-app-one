<?php

namespace NextelBR\Adapters\Request;

use NextelBR\Enumerators\NextelBRFormats;
use NextelBR\Enumerators\NextelInvoiceTypes;
use NextelBR\Exceptions\PlanNotEligible;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Models\Collections\Service;

class PreAdhesionRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        $customer           = data_get($service, 'customer');
        $cpf                = data_get($customer, 'cpf');
        $name               = data_get($customer, 'firstName', '') . ' ' . data_get($customer, 'lastName', '');
        $cachedInformations = data_get($extra, 'cachedInformations');
        $address            = data_get($extra, 'address');
        $cachedPlan         = data_get($cachedInformations, 'plans');
        $birthday           = DateConvertHelper::convertToStringFormat(
            $customer['birthday'],
            NextelBRFormats::DATES
        );

        if (self::isGenericAddress($address)) {
            $address['logradouro'] = data_get($service, 'customer.local');
            $address['bairro']     = data_get($service, 'customer.neighborhood');
        }

        $selectedPlan = $cachedPlan
            ->where('product', data_get($service, 'product'))
            ->where('offer', data_get($service, 'offer'))
            ->first();
        throw_if(is_null($selectedPlan), new PlanNotEligible());
        $address['complemento'] = data_get($customer, 'complement');
        $address['numero']      = data_get($customer, 'number');
        $contactNumber          = MsisdnHelper::removeCountryCode(
            CountryAbbreviation::BR,
            data_get($customer, 'mainPhone', '')
        );
        $debit                  = array_filter([
            "agenciaCodigo" => data_get($service, 'directDebit.agency'),
            "cc"            => data_get($service, 'directDebit.checkingAccount') . data_get($service, 'directDebit.bankOperation'),
            "ccDv"          => data_get($service, 'directDebit.checkingAccountDv'),
            "codigoBanco"   => data_get($service, 'directDebit.bankId')
        ]);
        if ($service->invoiceType == NextelInvoiceTypes::CARTAO_DE_CREDITO) {
            $invoiceType = NextelInvoiceTypes::CARTAO_M4U;
        } elseif ($service->invoiceType == NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST) {
            $invoiceType = NextelInvoiceTypes::DEBITO_AUTOMATICO_REQUEST;
        } else {
            $invoiceType = $service->invoiceType;
        }
        return [
            "cpf"                    => $cpf,
            "dadosBancarios"         => filled($debit) ? $debit : null,
            "dadosPlano"             => [
                "codigoOferta" => data_get($selectedPlan, 'nextel.codigoOferta'),
                "codigoPlano"  => data_get($selectedPlan, 'nextel.codigoPlano'),
                "codigoTabela" => data_get($selectedPlan, 'nextel.codigoTabela'),
                "nomePlano"    => data_get($selectedPlan, 'nextel.nomePlano'),
                "nomeTabela"   => data_get($selectedPlan, 'nextel.nomeTabela'),
                "valorThab"    => data_get($selectedPlan, 'nextel.valorThab'),
                "valorTotal"   => data_get($selectedPlan, 'nextel.valorTotal')
            ],
            "dataDeVencimentoFatura" => (int) $service->dueDate,
            "ddd"                    => $service->areaCode,
            "dddContato"             => $service->areaCode,
            "telefoneContato"        => substr($contactNumber, 2),
            "emailContato"           => data_get($customer, 'email'),
            "iccid"                  => $service->iccid,
            "endereco"               => $address,
            "formaPagamento"         => $invoiceType,
            "genero"                 => data_get($customer, 'gender'),
            "nascimento"             => $birthday,
            "nome"                   => $name,
            "nomeDaMae"              => data_get($customer, 'filiation', ''),
            "numeroPedido"           => data_get($service, 'operatorIdentifiers.numeroPedido'),
            "rg"                     => data_get($customer, 'rg', '')
        ];
    }

    private static function isGenericAddress($address)
    {
        return (empty($address['logradouro'] ?? null) || empty($address['bairro'] ?? null));
    }
}
