<?php


namespace Outsourced\ViaVarejo\Enumerators;

class ViaVarejoInvoiceType
{
    public const EMAIL      = 'EMAIL';
    public const VIA_POSTAL = 'VIA_POSTAL';

    public const INVOICE_TYPE = [
        self::EMAIL => 'E',
        self::VIA_POSTAL => 'C'
    ];

    public static function get(string $invoiceType): string
    {
        return self::INVOICE_TYPE[$invoiceType] ?? '';
    }
}
