<?php

namespace Buyback\Exportables;

final class AnalyticalReportIndexes
{
    public const DATE                          = 'Data Da Venda';
    public const HOUR                          = 'Hora Da Venda';
    public const SALETRANSACTION               = 'Venda Id';
    public const CPF                           = 'CPF Cliente';
    public const NAME                          = 'Nome Cliente';
    public const EMAIL                         = 'Email Cliente';
    public const CITY                          = 'Cidade Cliente';
    public const ZIPCODE                       = 'CEP Cliente';
    public const LOCAL                         = 'Logradouro';
    public const NUMBER                        = 'Numero';
    public const COMPLEMENT                    = 'Complemento';
    public const IMEI                          = 'IMEI';
    public const MODELID                       = 'Modelo Id';
    public const MODEL                         = 'Modelo';
    public const STORAGE                       = 'Capacidade';
    public const COLOR                         = 'Cor';
    public const PRICESALESMAN                 = 'Preco Vendedor';
    public const PRICEAPPRAISER                = 'Preco Tecnico';
    public const PRICECARRIER                  = 'Preco Transportadora';
    public const DIFF                          = 'Diferenca Entre Tecnico E Vendedor';
    public const PRICE                         = 'Preco A Pagar';
    public const CNPJ                          = 'CNPJ PDV';
    public const POINTOFSALE_SLUG              = 'Código PDV';
    public const PDV_CITY                      = 'Cidade PDV';
    public const PDV_LOCAL                     = 'Logradouro PDV';
    public const PDV_NUMBER                    = 'Numero PDV';
    public const PDV_ZIPCODE                   = 'CEP PDV';
    public const PDV_NETWORK                   = 'REDE PDV';
    public const NETWORK_OPERATION             = 'REDE Operacao';
    public const RECEIVED_AT                   = 'Recebido';
    public const STATUS                        = 'Status';
    public const STATUS_DEVICE                 = 'Status do Dispositivo';
    public const WAYBILL_ID                    = 'Romaneio';
    public const WAYBILL_DATE                  = 'Data do Romaneio';
    public const WAYBILL_WITHDRAWN             = 'Auditar Romaneio';
    public const WAYBILL                       = 'Lote';
    public const EVALUATIONS_BONUS_VALUES      = 'Detalhes do Incremento de Valor';
    public const EVALUATIONS_BONUS_SPONSORS    = 'Origem do Incremento de Valor';
    public const SALESMAN_NAME                 = 'Vendedor Avaliador';
    public const HAS_RECOMMENDATION            = 'Possui Indicação';
    public const RECOMMENDATION_REGISTRATION   = 'Indicação da Venda';
    public const QUESTIONS_ANSWERS_SALESMAN    = 'Perguntas x Respostas Vendedor';
    public const QUESTIONS_ANSWERS_TECHNICAL   = 'Perguntas x Respostas Técnico';
    public const PPRAISER_TECHNICIAN_EVALUATOR = 'Técnico validador';
    public const DATEAPPRAISER                 = 'Data da Avaliação';

    public const TYPE   = 'Tipo';
    public const REASON = 'Motivo';

    public static function headings(): array
    {
        return [
            self::DATE,
            self::HOUR,
            self::SALETRANSACTION,
            self::CPF,
            self::NAME,
            self::EMAIL,
            self::CITY,
            self::LOCAL,
            self::ZIPCODE,
            self::NUMBER,
            self::COMPLEMENT,
            self::IMEI,
            self::MODELID,
            self::MODEL,
            self::STORAGE,
            self::COLOR,
            self::PRICESALESMAN,
            self::PRICEAPPRAISER,
            self::PRICECARRIER,
            self::DIFF,
            self::CNPJ,
            self::POINTOFSALE_SLUG,
            self::PDV_CITY,
            self::PDV_LOCAL,
            self::PDV_NUMBER,
            self::PDV_ZIPCODE,
            self::PDV_NETWORK,
            self::NETWORK_OPERATION,
            self::RECEIVED_AT,
            self::PRICE,
            self::STATUS,
            self::STATUS_DEVICE,
            self::WAYBILL_ID,
            self::WAYBILL_DATE,
            self::WAYBILL_WITHDRAWN,
            self::WAYBILL,
            self::EVALUATIONS_BONUS_SPONSORS,
            self::EVALUATIONS_BONUS_VALUES,
            self::SALESMAN_NAME,
            self::HAS_RECOMMENDATION,
            self::RECOMMENDATION_REGISTRATION,
            self::QUESTIONS_ANSWERS_SALESMAN,
            self::QUESTIONS_ANSWERS_TECHNICAL,
            self::PPRAISER_TECHNICIAN_EVALUATOR,
            self::DATEAPPRAISER,
        ];
    }
}
