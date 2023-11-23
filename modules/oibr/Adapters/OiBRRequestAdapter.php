<?php

namespace OiBR\Adapters;

use OiBR\OiBRIdentifierNotFound;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class OiBRRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        $sale         = $service->sale;
        $customerName = "{$service->customer['firstName']} {$service->customer['lastName']}";
        try {
            $oiIdentifier = $sale->pointOfSale['providerIdentifiers'][Operations::OI];
        } catch (\ErrorException $exception) {
            throw new OiBRIdentifierNotFound();
        }
        $date = date('dmY', strtotime($service->customer['birthday']));
        return array_filter([
            'msisdn'              => $service->msisdn,
            'cpf'                 => $service->customer['cpf'],
            'numeroContato'       => MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $service->customer['mainPhone']),
            'token'               => $service->token,
            'cvv'                 => $service->cvv,
            'estabelecimento'     => $oiIdentifier ?? '',
            'codigoOferta'        => $service->product,
            'vendedor'            => $sale->user['cpf'],
            'identificadorAdesao' => $service->serviceTransaction,
            'cepEndCobranca'      => $service->customer['zipCode'],
            'numeroResidCobranca' => $service->customer['number'],
            'dataNascimento'      => $date,
            'tipoVendedor'        => 'CPF',
        ]);
    }
}
