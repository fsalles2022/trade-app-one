<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

class Siv3ResidencialAddressToHpAddress
{
    /**
     * @param mixed[] $residencialAddress
     * @return mixed[]
     */
    public static function adapt(?array $residencialAddress): ?array
    {
        if ($residencialAddress === null) {
            return $residencialAddress;
        }

        return [
            "id" => null,
            "address" => [
              "id" => null,
              "streetId" => null,
              "streetName" => $residencialAddress['address'],
              "postalCode" => $residencialAddress['postalCode'],
              "neighborhoodId" => null,
              "neighborhood" => $residencialAddress['neighborhood'],
              "cityId" => $residencialAddress['cityId'],
              "city" => $residencialAddress['city'],
              "stateId" => null,
              "stateAcronym" => $residencialAddress['stateAcronym'],
            ],
            "hpAddress" =>  [
              "id" => null,
              "address" => $residencialAddress['address'],
              "postalCode" => $residencialAddress['postalCode'],
              "number" => null,
              "complement" => null,
              "neighborhood" => $residencialAddress['neighborhood'],
              "cityId" => $residencialAddress['cityId'],
              "city" => $residencialAddress['city'],
              "state" => $residencialAddress['stateAcronym'],
            ]
        ];
    }
}
