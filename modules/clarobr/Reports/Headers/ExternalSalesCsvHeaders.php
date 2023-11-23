<?php

declare(strict_types=1);

namespace ClaroBR\Reports\Headers;

class ExternalSalesCsvHeaders
{
    private const HEADERS = [
        'Tipo de Serviço',
        'DDD do Cliente',
        'Número da Linha do Cliente',
        'ICCID',
        'Cliente (CPF)',
        'E-mail do Cliente',
        'Vendedor (CPF)',
        'Nome do Vendedor',
        'DDD do Vendedor',
        'Código do Ponto de Venda',
        'Nome do Ponto de Venda',
        'Cod Regional Vendedor',
        'Nome Regional Vendedor',
        'Rede',
        'Data da Indicação',
        'Hora da Indicação'
    ];

    /** @return string[] */
    public static function headings(): array
    {
        return [self::HEADERS];
    }
}
