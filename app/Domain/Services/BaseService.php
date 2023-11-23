<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Exceptions\BusinessExceptions\ServiceInvalidException;

/**
 * @property NetworkService networkService
 * @property PointOfSaleService pointOfSaleService
 * @property RoleService roleService
 * @property SaleService saleService
 * @property SaleImportService saleImportService
 * @property UserService userService
 * @property PortfolioService portfolioService
 * @property UserThirdPartyRegistrationService userThirdPartyRegistrationService
 * @property HierarchyService hierarchyService
 * @property UserAuthAlternatesService userAuthAlternatesService
 */
class BaseService
{
    const SERVICES = [
        'networkService'                    => NetworkService::class,
        'pointOfSaleService'                => PointOfSaleService::class,
        'roleService'                       => RoleService::class,
        'saleService'                       => SaleService::class,
        'saleImportService'                 => SaleImportService::class,
        'userService'                       => UserService::class,
        'portfolioService'                  => PortfolioService::class,
        'hierarchyService'                  => HierarchyService::class,
        'userThirdPartyRegistrationService' => UserThirdPartyRegistrationService::class,
        'userAuthAlternatesService'         => UserAuthAlternatesService::class,
    ];

    public function __get($service)
    {
        if (! array_key_exists($service, self::SERVICES)) {
            throw new ServiceInvalidException();
        }

        $classname = self::SERVICES[$service];

        return app()->make($classname);
    }
}
