<?php

namespace TradeAppOne\Domain\Factories;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\Sale\RequirementsChainForSale;
use TradeAppOne\Exceptions\BusinessExceptions\OperationNoExists;
use TradeAppOne\Exceptions\BusinessExceptions\OperatorNoExists;

class ServicesFactory
{
    public static $AVALIABLE_SERVICES = Operations::SECTORS;

    /** @throws */
    public static function make(array $service): Service
    {
        $operator  = mb_strtoupper($service['operator']);
        $operation = mb_strtoupper($service['operation']);
        $sector    = null;
        foreach (self::$AVALIABLE_SERVICES as $registeredSector => $registeredOperator) {
            if (array_key_exists($operator, $registeredOperator)) {
                $sector = $registeredSector;
            }
        }

        throw_if(! array_key_exists($sector, self::$AVALIABLE_SERVICES), new OperatorNoExists());
        throw_if(! array_key_exists($operator, self::$AVALIABLE_SERVICES[$sector]), new OperatorNoExists($operator));
        throw_if(
            ! array_key_exists($operation, self::$AVALIABLE_SERVICES[$sector][$operator]),
            new OperationNoExists($operation)
        );

        $service['sector'] = $sector;
        $service['status'] = ServiceStatus::PENDING_SUBMISSION;
        $service           = PriceFactory::make($service);
        $service           = RequirementsChainForSale::apply($service);

        return new self::$AVALIABLE_SERVICES[$sector][$operator][$operation]($service);
    }
}
