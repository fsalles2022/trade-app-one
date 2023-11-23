<?php


namespace Voucher\tests\Fixture;

use ClaroBR\Models\ControleFacil;

class VoucherOperationsFixtures
{

    public static function imei(): string
    {
        return '865396912904816';
    }

    public static function validImeiForTesting(): string
    {
        return '000000000000110';
    }

    public static function fakeServiceTransaction(): string
    {
        return '202006051348171250-7';
    }

    public static function metadata(): array
    {
        return [
            'caixa' => '19292',
            'cupom' => 'ASDWQESA'
        ];
    }

    public static function customerCpf(): string
    {
        return '12345612390';
    }

    public static function burnedObject(): array
    {
        return [
            'current' => [
                'imei' => self::imei(),
                'metadata' => self::metadata(),
                'createdAt' => ''
            ],
            'log' => []
        ];
    }

    public static function notBurnedObject(): array
    {
        return [
            'current' => null,
            'log' => []
        ];
    }

    public static function discountObject(): array
    {
        return [
            'id' => 300,
            'title' => 'Desconto da Primavera',
            'discount' => 200
        ];
    }

    public static function phone(): string
    {
        return '11999998888';
    }

    public static function newService(): array
    {
        return [
            "operator" => "CLARO",
            "sector" => "TELECOMMUNICATION",
            "plan" => "Controle Super 4GB",
            "transaction_id" => "202007061057533482-0",
            "value" => 25,
            "imei" => "000000000000110",
            "model" => "I2GO CARREGADOR VEICULAR 2 SAÕDAS BASIC -PRETO"
        ];
    }
}
