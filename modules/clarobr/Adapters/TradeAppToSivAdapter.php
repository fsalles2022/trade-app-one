<?php

namespace ClaroBR\Adapters;

use ClaroBR\Enumerators\ClaroBRDependents;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Models\Collections\Service;

class TradeAppToSivAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        return [
            'service'             => array_filter([
                'plano_id'              => $service->product ?? '',
                'promocao_id'           => self::choicePromotion($service),
                'msisdn'                => $service->msisdn ?? '',
                'tipo_fatura'           => $service->invoiceType ?? '',
                'vencimento_id'         => $service->dueDate ?? '',
                'ddd'                   => $service->areaCode ?? '',
                'iccid'                 => $service->iccid ?? '',
                'portabilidade'         => $service->portedNumber ?? '',
                'portabilidade_token'   => $service->portedNumberToken ?? '',
                'banco_id'              => $service->bankId ?? '',
                'agencia'               => $service->agency ?? '',
                'conta_corrente'        => $service->checkingAccount ?? '',

                "preco_pre"            => $service->device['priceWithout'] ?? '',
                "preco_aparelho_plano" => $service->device['priceWith'] ?? '',
                "nome_aparelho"        => $service->device['model'] ?? '',
                "imei"                 => $service->imei ?? '',
                "chip_combo"           => (int) $service->chipCombo ?? 0,
                'dependentes'          => self::mapDependents($service->dependents),
                'trade_hub'            => $service->tradeHub ?? null,
            ]),
            'customer'            => [
                'nome'                => "{$service->customer['firstName']} {$service->customer['lastName']}",
                'cpf'                 => $service->customer['cpf'],
                'email'               => $service->customer['email'] ?? '',
                'genero'              => $service->customer['gender'] ?? '',
                'data_nascimento'     => $service->customer['birthday'] ?? '',
                'filiacao'            => $service->customer['filiation'] ?? '',
                'telefone_principal'  => $service->customer['mainPhone'] ?? '',
                'telefone_secundario' => $service->customer['secondaryPhone'] ?? '',
                'faixa_salarial_id'   => 6,
                'profissao_id'        => 2,
                'estado_civil_id'     => 1,
                'rg'                  => $service->customer['rg'] ?? '',
                'rg_data_emissao'     => $service->customer['rgDate'] ?? '',
                'rg_orgao_expedidor'  => $service->customer['rgLocal'] ?? '',
                'logradouro'          => $service->customer['local'] ?? '',
                'logradouro_tipo_id'  => $service->customer['localId'] ?? '',
                'cep'                 => $service->customer['zipCode'] ?? '',
                'numero'              => $service->customer['number'] ?? '',
                'cidade'              => $service->customer['city'] ?? '',
                'uf'                  => $service->customer['state'] ?? '',
                'bairro'              => $service->customer['neighborhood'] ?? '',
                'complemento'         => data_get($service->customer, 'complement'),
            ],
            'operatorIdentifiers' => $service->operatorIdentifiers ?? []
        ];
    }

    private static function mapDependents(?array $dependents): array
    {
        $mappedDependents = [];
        if ($dependents) {
            foreach ($dependents as $dependent) {
                $msisdnDependent = MsisdnHelper::removeCountryCode(
                    MsisdnHelper::BR,
                    data_get($dependent, 'msisdn', '')
                );
                $mappedDependent = array_filter([
                    "tipo_servico"  => ClaroBRDependents::translateModeToRequest(data_get($dependent, 'mode')),
                    "plano_tipo"    => ClaroBRDependents::translateTypeToRequest(data_get($dependent, 'type')),
                    "plano_id"      => data_get($dependent, 'product'),
                    "promocao_id"   => data_get($dependent, 'promotion.product'),
                    "iccid"         => data_get($dependent, 'iccid'),
                    "numero_base"   => $msisdnDependent,
                    "portabilidade" => data_get($dependent, 'portedNumber'),
                ]);
                array_push($mappedDependents, $mappedDependent);
            }
        }
        return $mappedDependents;
    }

    public static function choicePromotion($service)
    {
        if ($service->chipCombo) {
            return '';
        }
        return data_get($service, 'promotion.product');
    }
}
