<?php

namespace OiBR\Connection;

final class OiBRRoutes
{
    const REGISTER_CREDIT_CARD      = 'eldorado/v1/cc';
    const REGISTER_CREDIT_CARD_PROD = 'v1/cc';

    public static function controleCartaEligibility(string $msisdn): string
    {
        return "oicontrole/rs/v1/contrato/elegibilidade/{$msisdn}.msisdn";
    }

    public static function queryCompliance(string $msisdn, int $days): string
    {
        return "oicontrole/rs/v1/contrato/{$msisdn}/adimplencia/{$days}";
    }

    public static function queryControleCartaoStatus(string $identifier): string
    {
        return "oicontrole/rs/v1/adesao/{$identifier}/status";
    }

    public static function controleCartaoQuery(string $identifier): string
    {
        return "oicontrole/rs/v1/adesao/{$identifier}/completo";
    }

    public static function postControleCartaEligibility(string $msisdn): string
    {
        return "adesao-boleto/v1/adesao/msisdn/{$msisdn}";
    }

    public static function controleBoletoEligibility(string $msisdn): string
    {
        return "adesao-boleto/v1/msisdn/{$msisdn}/elegibilidade";
    }

    public static function getPlans(string $invoiceType): string
    {
        return "adesao-boleto/v1/adesao/meioPagamento/{$invoiceType}/ofertas";
    }

    public static function postControleBoleto(string $msisdn): string
    {
        return "adesao-boleto/v1/adesao/msisdn/{$msisdn}";
    }

    public static function queryControleBoletoStatus(string $msisdn): string
    {
        return "adesao-boleto/v1/adesao/msisdn/{$msisdn}";
    }

    public static function queryControleBoleto(string $msisdn): string
    {
        return "posvenda/v1/msisdn/{$msisdn}";
    }

    public static function queryControleBoletoCycle(string $msisdn): string
    {
        return "posvenda/v1/msisdn/{$msisdn}/ciclo/status";
    }

    public static function postControleCartaoMigration(): string
    {
        return 'oicontrole/rs/v3/contrato';
    }

    public static function postControleCartaoActivation(string $uuid): string
    {
        return "oicontrole/cartao/adesao/v1/$uuid/ativacaodechip";
    }

    public static function getControleCartaoStatus(string $uuid): string
    {
        return "oicontrole/cartao/adesao/v1/$uuid/status";
    }
}
