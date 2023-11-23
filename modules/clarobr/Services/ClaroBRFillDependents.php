<?php

namespace ClaroBR\Services;

use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Enumerators\ClaroBRDependents;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;

class ClaroBRFillDependents
{
    protected $connection;

    public function __construct(SivConnectionInterface $sivConnection)
    {
        $this->connection = $sivConnection;
    }

    public function fill(string $cpf, $originalDependents, $areaCode)
    {
        $dependents = [];
        foreach ($originalDependents as $dependent) {
            $plans             = $this->connection->getPlans($cpf, [
                'id'  => $dependent['product'],
                'ddd' => $areaCode
            ])->toArray();
            $dependentProduct  = (new ClaroBRMapSale())
                ->extractProductAttributes($plans, $dependent['product'], $areaCode, $dependent['promotion'])
                ->toArray();
            $dependent['mode'] = ClaroBRDependents::translateMode($dependent['mode']);
            $dependent['type'] = ClaroBRDependents::translateType($dependent['type']);
            if ($msisdn = data_get($dependent, 'msisdn')) {
                $dependent['msisdn'] = MsisdnHelper::addCountryCode(MsisdnHelper::BR, $msisdn);
            }
            $dependent = array_merge($dependent, $dependentProduct);
            array_push($dependents, $dependent);
        }
        return $dependents;
    }
}
