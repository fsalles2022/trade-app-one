<?php

namespace NextelBR\Adapters\Request;

use NextelBR\Enumerators\NextelBRConstants;
use NextelBR\Enumerators\NextelBRFormats;
use NextelBR\Exceptions\NextelBRIdentifiersNotFound;
use TradeAppOne\Domain\Enumerators\Operations;

class NextelBREligibilityRequestAdapter
{
    public static function adapt(array $payload, $extra = null)
    {
        $codLoja     = data_get($extra, 'providerIdentifiers.' . Operations::NEXTEL . '.' . NextelBRConstants::POINT_OF_SALE_COD);
        $vendedorCod = data_get($extra, 'providerIdentifiers.' . Operations::NEXTEL . '.' . NextelBRConstants::POINT_OF_SALE_REF);
        $birthday    = date(NextelBRFormats::DATES, strtotime(data_get($payload, 'customer.birthday')));
        $name        = data_get($payload, 'customer.firstName') . ' ' . data_get($payload, 'customer.lastName');

        if (is_null($codLoja) || is_null($vendedorCod)) {
            throw new NextelBRIdentifiersNotFound();
        }
        return [
            "codLoja"        => $codLoja,
            "vendedorCod"    => $vendedorCod,
            "vendedorCPF"    => data_get($payload, 'user'),
            "cpf"            => data_get($payload, 'customer.cpf'),
            "nome"           => $name,
            "dataNascimento" => $birthday,
            "genero"         => data_get($payload, 'customer.gender')
        ];
    }
}
