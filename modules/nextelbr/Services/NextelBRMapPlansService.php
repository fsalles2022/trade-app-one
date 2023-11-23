<?php

namespace NextelBR\Services;

use Illuminate\Support\Collection;
use NextelBR\Enumerators\NextelBRConstants;
use NextelBR\Enumerators\NextelInvoiceTypes;
use TradeAppOne\Domain\Adapters\MapPlans;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;

class NextelBRMapPlansService implements MapPlans
{
    const NEXTEL_BOLETO      = [NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST, NextelInvoiceTypes::BOLETO];
    const NEXTEL_CREDIT_CARD = [NextelInvoiceTypes::CARTAO_DE_CREDITO];
    const ADHESION_VALUE     = 'adhesionValue';

    public static function map(array $plans, $filters = []): Collection
    {
        $operation    = data_get($filters, 'operation');
        $flattenPlans = self::flatPlans($plans);

        if ($operation == Operations::NEXTEL_CONTROLE_CARTAO) {
            $flattenPlans = $flattenPlans->where('operation', Operations::NEXTEL_CONTROLE_CARTAO);
        }
        if ($operation == Operations::NEXTEL_CONTROLE_BOLETO) {
            $flattenPlans = $flattenPlans->where('operation', Operations::NEXTEL_CONTROLE_BOLETO);
        }
        return $flattenPlans->values();
    }

    private static function flatPlans(array $plans): Collection
    {
        $flattenPlans = collect();
        foreach ($plans as $plan) {
            $flattenPlan['operator'] = Operations::NEXTEL;
            $flattenPlan['product']  = data_get($plan, NextelBRConstants::PRODUCT_KEY);
            $flattenPlan['idPlano']  = data_get($plan, 'idPlano');
            $flattenPlan['offer']    = data_get($plan, 'idOferta');
            $flattenPlan['label']    = data_get($plan, 'descricao');
            foreach (data_get($plan, 'tabelas') as $table) {
                $flattenPlan['table']['label']         = data_get($table, 'nomeTabela');
                $flattenPlan['table']['id']            = data_get($table, 'idTabela');
                $flattenPlan['price']                  = data_get($table, 'valorComDesconto', 0) / 100;
                $flattenPlan['adhesionValue']          = data_get($table, 'valorThab', 0) / 100;
                $flattenPlan['portability']            = data_get($table, 'portabilidade');
                $flattenPlan['invoiceType']            = data_get($table, 'formasDePagamento', []);
                $flattenPlan['nextel']['valorTotal']   = data_get($table, 'valorTotal', 0);
                $flattenPlan['nextel']['valorThab']    = data_get($table, 'valorThab', 0);
                $flattenPlan['nextel']['nomeTabela']   = data_get($table, 'nomeTabela', 0);
                $flattenPlan['nextel']['codigoTabela'] = data_get($table, 'idTabela');
                $flattenPlan['nextel']['codigoPlano']  = data_get($plan, 'idPlano');
                $flattenPlan['nextel']['codigoOferta'] = data_get($plan, 'idOferta');
                $flattenPlan['nextel']['nomePlano']    = data_get($plan, 'nomePlano');

                $details = [
                    trans(
                        'nextelBR::messages.planDetails.internet',
                        ['internet' => data_get($table, 'franquiaDeDados')]
                    ),
                    trans(
                        'nextelBR::messages.planDetails.bonusInternet',
                        ['bonusInternet' => data_get($table, 'bonusInternet')]
                    ),
                    trans('nextelBR::messages.planDetails.voice', ['voice' => data_get($table, 'franquiaDeVoz')]),
                ];
                if (data_get($table, 'flatFee')) {
                    array_push(
                        $details,
                        trans('nextelBR::messages.planDetails.fee', ['period' => data_get($table, 'periodoFlatFee')])
                    );
                }
                $flattenPlan['details'] = $details;

                if (in_array(NextelInvoiceTypes::CARTAO_DE_CREDITO, $flattenPlan['invoiceType'])) {
                    $flattenPlan['operation'] = Operations::NEXTEL_CONTROLE_CARTAO;
                    $flattenPlans->push($flattenPlan);
                }
                if (in_array(NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST, $flattenPlan['invoiceType'])
                    || in_array(NextelInvoiceTypes::BOLETO, $flattenPlan['invoiceType'])) {
                    $flattenPlan['operation'] = Operations::NEXTEL_CONTROLE_BOLETO;
                    $flattenPlans->push($flattenPlan);
                }
            }
        }
        return $flattenPlans;
    }
}
