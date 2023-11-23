<?php


namespace TradeAppOne\Domain\Adapters;

use TradeAppOne\Domain\Models\Tables\Service;

class ProductWithOperationsAdapter
{
    public static function adapter(array $services)
    {
        $formatted = [];
        foreach ($services as $sector => $operators) {
            $map['id']        = $sector;
            $map['label']     = trans("operations.$sector");
            $map['operators'] = [];
            foreach ($operators as $operator => $operations) {
                $op['id']         = $operator;
                $op['label']      = trans("operations.$operator");
                $op['operations'] = [];
                foreach ($operations as $operation) {
                    $label              = Service::query()
                        ->where('sector', '=', $sector)
                        ->where('operator', '=', $operator)
                        ->where('operation', '=', $operation)
                        ->get()->pluck('label')->first();
                    $o['id']            = $operation;
                    $o['label']         = $label ?? trans("operations.$operation.label");
                    $op['operations'][] = $o;
                }
                $map['operators'][] = $op;
            }
            $formatted[] = $map;
        }
        return $formatted;
    }
}
