<?php


namespace Generali\Assistance\Connection;

final class GeneraliRoutes
{
    public const NAMESPACE = '/generali/v1/';
    public const PRODUCT   = 'produto';
    public const PLAN      = 'plano';
    public const COVERAGE  = 'plano/coberturas';


    public static function activate(): string
    {
        return self::NAMESPACE . 'subscription/';
    }

    public static function calcPremium(): string
    {
        return self::NAMESPACE . 'calc-premium';
    }

    public static function calcRefund(): string
    {
        return self::NAMESPACE . 'calc-refund';
    }

    public static function cancel(): string
    {
        return self::NAMESPACE . 'cancel';
    }

    public static function eligibility():string
    {
        return self::NAMESPACE . 'eligibility';
    }

    public static function transactionByReference(string $reference): string
    {
        return self::NAMESPACE . "transaction/$reference";
    }
}
