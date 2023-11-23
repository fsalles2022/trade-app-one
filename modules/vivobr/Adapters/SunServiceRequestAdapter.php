<?php

namespace VivoBR\Adapters;

use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use VivoBR\Enumerators\VivoInvoiceType;

class SunServiceRequestAdapter
{
    public static function adapt(Service $service): array
    {
        $sale = $service->sale;
        if ($service->portedNumber && empty($service->areaCode)) {
            $service->areaCode = substr($service->portedNumber, 0, 2);
        }
        if ($service->msisdn && empty($service->areaCode)) {
            $service->areaCode = substr($service->msisdn, 0, 2);
        }
        if ($service->mode === 'ACTIVATION' || $service->mode === 'PORTABILITY') {
            $type = 'ALTA';
        } else {
            $type = 'MIGRACAO';
        }
        if ($service->operation === Operations::VIVO_PRE) {
            $service->invoiceType = 'PRE_PAGO';
        }
        if ($service->invoiceType === VivoInvoiceType::DEBITO_AUTOMATICO) {
            $service->invoiceType = VivoInvoiceType::EMAIL;
            $service->paymentType = VivoInvoiceType::DEBITO_AUTOMATICO;
        }
        return array_filter([
            'cnpjPdv' => $sale->pointOfSale['cnpj'],
            'cpfVendedor' => $sale->user['cpf'],
            'observacoes' => $service->comments ?? '',
            'm4uParentUrl' => '*',
            'vendaFaseada' => $service->vendaFaseada ?? null,
            'servicos' => [
                array_filter([
                    'idPlano' => $service->product ?? '',
                    'ddd' => $service->areaCode ?? '',
                    'tipoServico' => $type,
                    'portabilidade' => filled($service->portedNumber),
                    'numeroPortabilidade' => $service->portedNumber ?? '',
                    'operadora' => (int) ($service->fromOperator ?? 0),
                    'fidelizacao' => $service->loyalty ?? false,
                    'iccid' => $service->iccid ?? '',
                    'imei' => $service->imei ?? '',
                    'numeroAcesso' => $service->msisdn ?? '',
                    'tipoFatura' => $service->invoiceType ?? '',
                    'ativarPre' => $service->preActivation ?? '',
                    'vencimento' => $service->dueDate ?? '',
                    'formatoPagamento' => $service->paymentType ?? '',
                    'dadosBancarios' => array_filter([
                        'idBanco' => $service->bankId ?? '',
                        'numeroAgencia' => $service->agency ?? '',
                        'numeroConta' => $service->checkingAccount ?? '',
                        'tipoConta'   => $service->accountType ?? ''
                    ])
                ])
            ],
            'pessoa' => array_filter([
                'cpf' => $service->customer['cpf'],
                'nome' => $service->customer['firstName'] . ' ' . $service->customer['lastName'],
                'sexo' => $service->customer['gender'] ?? '',
                'filiacao' => $service->customer['filiation'] ?? '',
                'dataNascimento' => $service->customer['birthday'] ?? '',
                'email' => $service->customer['email'] ?? '',
                'cep' => str_replace('-', '', $service->customer['zipCode'] ?? ''),
                'logradouro' => $service->customer['local'] ?? '',
                'numero' => $service->customer['number'] ?? '',
                'semNumero' => empty($service->customer['number']),
                'bairro'         => $service->customer['neighborhood'] ?? '',
                'cidade'         => $service->customer['city'] ?? '',
                'uf'             => $service->customer['state'] ?? '',
                'complemento'    => $service->customer['additionalAddressData'] ?? '',
                'telefone1' => MsisdnHelper::removeCountryCode(
                    CountryAbbreviation::BR,
                    $service->customer['mainPhone']
                    ?? ''
                ),
                'telefone2' => MsisdnHelper::removeCountryCode(
                    CountryAbbreviation::BR,
                    $service->customer['secondaryPhone']
                    ?? ''
                ),
            ]),
            'biometria' => $service->biometrics ?? false
        ]);
    }
}
