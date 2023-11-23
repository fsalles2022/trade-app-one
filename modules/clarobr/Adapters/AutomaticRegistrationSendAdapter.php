<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

use ClaroBR\Enumerators\ClaroRoles;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Models\Tables\User;

class AutomaticRegistrationSendAdapter
{
    /**
     * @param mixed[] $additionalRequestData
     * @return mixed[]
     */
    public static function adapt(User $user, array $additionalRequestData = []): array
    {
        $phone = MsisdnHelper::addDialCountryCode(
            CountryAbbreviation::BR,
            data_get($additionalRequestData, 'usuario.telefone', '11000000000')
        );

        return [
            'user' => [
                'name' => self::getUserFullName($user),
                'cpf' => data_get($user, 'cpf', '00000000000'),
                'email' => data_get($user, 'email', ''),
                'birthdate' => data_get($user, 'birthday', now()->format('Y-m-d')),
                'street' => data_get($additionalRequestData, 'usuario.endereco.rua', ''),
                'city' => data_get($additionalRequestData, 'usuario.endereco.cidade', ''),
                'state' => data_get($additionalRequestData, 'usuario.endereco.uf', ''),
                'neighborhood' => data_get($additionalRequestData, 'usuario.endereco.bairro', ''),
                'postalcode' => data_get($additionalRequestData, 'usuario.endereco.cep', '00000000'),
                'number' => data_get($additionalRequestData, 'usuario.endereco.numero', '000'),
                'complement' => data_get($additionalRequestData, 'usuario.endereco.complemento', ''),
                'phone' => $phone,
                'role' => ClaroRoles::VENDEDOR,
                'operation' => data_get($additionalRequestData, 'centralizador.operacao'),
                'idpdv' => data_get($additionalRequestData, 'pdv.idpdv', '00000')
            ],
            'pointOfSale' => [
                'code' => self::adaptPdvCode(data_get($additionalRequestData, 'pdv.codigo'))
            ]
        ];
    }

    private static function getUserFullName(User $user): string
    {
        return data_get($user, 'firstName', '') . ' ' .
            data_get($user, 'lastName', '');
    }

    private static function adaptPdvCode(?string $pdvCode): string
    {
        return str_contains($pdvCode, '-') === false ? $pdvCode : current(explode('-', $pdvCode));
    }
}
