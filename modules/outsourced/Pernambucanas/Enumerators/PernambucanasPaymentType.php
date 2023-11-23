<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Enumerators;

use ClaroBR\Enumerators\ClaroInvoiceTypes;
use NextelBR\Enumerators\NextelInvoiceTypes;
use OiBR\Enumerators\OiBRInvoiceTypes;
use TimBR\Enumerators\TimBRInvoiceTypes;
use TradeAppOne\Domain\Enumerators\Operations;
use VivoBR\Enumerators\VivoInvoiceType;

class PernambucanasPaymentType
{
    public const BOLETO = 'BOLETO';
    public const CARTAO = 'CARTÃO';
    public const DEBITO = 'DEBITO';

    public const OPTIONS = [
        Operations::CLARO => [
            ClaroInvoiceTypes::EMAIL => self::BOLETO,
            ClaroInvoiceTypes::VIA_POSTAL => self::BOLETO,
            ClaroInvoiceTypes::CARTAO_CREDITO => self::CARTAO,
            ClaroInvoiceTypes::DEBITO_AUTOMATICO => self::DEBITO
        ],
        Operations::VIVO => [
            VivoInvoiceType::EMAIL => self::BOLETO,
            VivoInvoiceType::VIA_POSTAL => self::BOLETO,
            VivoInvoiceType::CARTAO_CREDITO => self::CARTAO,
            VivoInvoiceType::DEBITO_AUTOMATICO => self::DEBITO
        ],
        Operations::NEXTEL => [
            NextelInvoiceTypes::BOLETO => self::BOLETO,
            NextelInvoiceTypes::CARTAO_M4U => self::CARTAO,
            NextelInvoiceTypes::CARTAO_DE_CREDITO => self::CARTAO,
            NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST => self::DEBITO,
            NextelInvoiceTypes::DEBITO_AUTOMATICO_REQUEST => self::DEBITO,
        ],
        Operations::TIM => [
            TimBRInvoiceTypes::FATURA => self::BOLETO,
            TimBRInvoiceTypes::DEBITO_AUTOMATICO => self::DEBITO
        ],
        Operations::OI => [
            OiBRInvoiceTypes::BOLETO => self::BOLETO,
            OiBRInvoiceTypes::CARTAO_CREDITO => self::CARTAO
        ],
        Operations::SURF_PERNAMBUCANAS => [
            'default' => self::CARTAO
        ],
    ];

    public static function get(?string $operator, ?string $invoiceType): string
    {
        return self::OPTIONS[$operator][$invoiceType] ?? '';
    }
}
