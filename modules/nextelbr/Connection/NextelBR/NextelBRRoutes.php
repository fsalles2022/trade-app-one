<?php

namespace NextelBR\Connection\NextelBR;

final class NextelBRRoutes
{
    const V1      = 'v1';
    const CHANNEL = 'tradeup';
    const PREFIX  = 'nextel-controle/varejo/' . self::V1;

    public static function cep(string $cep)
    {
        return self::PREFIX . '/servicos/' . self::CHANNEL . '/cliente/endereco/' . $cep;
    }

    public static function paymentDates()
    {
        return self::PREFIX . '/servicos/' . self::CHANNEL . '/datas/pagamento';
    }

    public static function banks(): string
    {
        return self::PREFIX . '/servicos/' . self::CHANNEL . '/banco/lista';
    }

    public static function products()
    {
        return self::PREFIX . '/' . self::CHANNEL . '/adesao/planos';
    }

    public static function portabilityDates()
    {
        return self::PREFIX . '/servicos/' . self::CHANNEL . '/operadora/portabilidade/datas';
    }

    public static function portabilityOperators()
    {
        return self::PREFIX . '/servicos/' . self::CHANNEL . '/operadora/lista';
    }

    public static function eligibility()
    {
        return self::PREFIX . '/' . self::CHANNEL . '/adesao/elegibilidade';
    }

    public static function preAdhesion(string $protocol)
    {
        return self::PREFIX . '/' . self::CHANNEL . '/adesao/' . $protocol . '/pre';
    }

    public static function adhesion(string $protocol)
    {
        return self::PREFIX . '/' . self::CHANNEL . '/adesao/' . $protocol . '/conclui';
    }

    public static function validateBankData()
    {
        return self::PREFIX . '/servicos/'. self::CHANNEL . '/banco/validacao';
    }
}
