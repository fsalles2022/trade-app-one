<?php

namespace TradeAppOne\Domain\Rules;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OperatorValidationRule extends Rule
{
    public function passes($attribute, $value, $parameters, $values)
    {
        $servicesPayload = collect($values->getData()['services']);
        $operations      = $servicesPayload->pluck('operation')->unique();
        $operators       = $servicesPayload->pluck('operator')->unique();

        $pointOfSale = Auth::user()->pointsOfSale()->first();

        $services = $pointOfSale->services->isNotEmpty()
            ? $pointOfSale->services()
            : $pointOfSale->network->services();

        $numberOfPermissionsFound = $services
            ->whereIn('operator', $operators)
            ->whereIn('operation', $operations)
            ->get()
            ->unique('operation')
            ->count();

        return $numberOfPermissionsFound === $operations->count();
    }

    public function message()
    {
        return trans('validation.has_operator_permission');
    }
}
