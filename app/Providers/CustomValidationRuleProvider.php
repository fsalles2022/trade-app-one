<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Rules\Validation\NameValidation;
use TradeAppOne\Domain\Rules\Validation\UndefinedValidation;

class CustomValidationRuleProvider extends ServiceProvider
{

    public function boot()
    {
        $namespace = 'TradeAppOne\Domain\Rules';
        $validator = $this->app['validator'];

        $validator->extend('name', NameValidation::class."@passesValidationProvider");
        $validator->extend(UndefinedValidation::KEY, UndefinedValidation::class . "@passesValidationProvider");
        $validator->extend('cpf', "$namespace\CpfValidationRule@passes");
        $validator->extend('cnpj', "$namespace\CnpjValidationRule@passes");
        $validator->extend('area_code_prefix', "$namespace\AreaCodePrefixValidationRule@passes");
        $validator->extend('point_of_sale', "$namespace\PointOfSaleValidationRule@passes");
        $validator->extend('states_br', "$namespace\StatesBRValidationRule@passes");
        $validator->extend('permissions', "$namespace\GivePermissionsRule@passes");
        $validator->extend('hasOperatorPermission', "$namespace\OperatorValidationRule@passes");
        $validator->extend('file_size', "$namespace\FileValidationRule@passes");
    }
}
