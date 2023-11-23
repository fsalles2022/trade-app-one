<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

use Illuminate\Contracts\Auth\Authenticatable;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class Siv3ExternalSaleRequestAdapter
{
    /**
     * @param mixed[] $saleExternal
     * @return mixed[]
     */
    public static function adapt(array $saleExternal, Authenticatable $userLogged): array
    {
        $salesmanName             = "{$userLogged['firstName']} {$userLogged['lastName']}";
        $pointOfSaleUserLogged    = $userLogged->pointsOfSale()->first();
        $pointOfSaleCode          = self::extractPointOfSaleCode($pointOfSaleUserLogged);
        $pointOfSaleHierarchyName = $userLogged->hierarchies()->first();
        $hierachyId               = sprintf(data_get($pointOfSaleUserLogged, 'hierarchyId', ''));
        $msisdn                   = data_get($saleExternal, 'msisdn', '');
        $areaCode                 = data_get($saleExternal, 'areaCode', null);

        return [
            'mode' => data_get($saleExternal, 'mode', ''),
            'areaCode' => $areaCode ?? MsisdnHelper::getAreaCode($msisdn),
            'msisdn' => substr($msisdn, 2, MsisdnHelper::MIN_LENGTH),
            'iccid' => data_get($saleExternal, 'iccid', ''),
            'customerCpf' => data_get($saleExternal, 'customerCpf', ''),
            'salesmanCpf' => data_get($userLogged, 'cpf', ''),
            'pointOfSaleCode' => $pointOfSaleCode,
            'networkSlug' => data_get($userLogged->getNetwork(), 'slug'),
            'email' => data_get($saleExternal, 'email', ''),
            'salesmanAreaCode' => data_get($userLogged, 'areaCode', ''),
            'pointOfSaleHierarchyId' => $hierachyId,
            'salesmanName' => $salesmanName,
            'pointOfSaleName' => data_get($pointOfSaleUserLogged, 'label', ''),
            'pointOfSaleHierarchyName' => data_get($pointOfSaleHierarchyName, 'label', '')
        ];
    }

    private static function extractPointOfSaleCode(?PointOfSale $pointOfSale): string
    {
        return data_get($pointOfSale, 'providerIdentifiers.'. Operations::CLARO, '');
    }
}
