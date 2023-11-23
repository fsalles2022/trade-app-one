<?php

namespace Gateway\Components;

use Gateway\Helpers\ValidateModelTrait;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;

/**
 * @property string customerIdentity
 * @property int cpf
 * @property string name
 * @property string address
 * @property string address2
 * @property string city
 * @property string state
 * @property string postalCode
 * @property string country
 * @property string phone
 * @property string email
 */
class Customer
{
    use ValidateModelTrait;

    protected $attributes;

    public static function fill(array $attributes): Customer
    {
        $customer = new static;

        $customer->validate($attributes);

        $customer->attributes       = $attributes;
        $customer->customerIdentity = $attributes['customerIdentity'] ?? $attributes['cpf'];
        $customer->cpf              = data_get($attributes, 'cpf');
        $customer->name             = $attributes['name'] ?? "{$attributes['firstName']} {$attributes['lastName']}";
        $customer->address          = data_get($attributes, 'address');
        $customer->address2         = data_get($attributes, 'address2');
        $customer->city             = data_get($attributes, 'city');
        $customer->postalCode       = data_get($attributes, 'postalCode');
        $customer->country          = data_get($attributes, 'address', 'BRA');
        $customer->phone            = data_get($attributes, 'phone');
        $customer->email            = data_get($attributes, 'email');

        return $customer;
    }

    private static function rules(): array
    {
        return [
            "customerIdentity" => 'sometimes|string|max:11',
            "cpf" => 'required|cpf',
            "name" => 'required_without:firstName,lastName|string',
            "firstName" => 'required_without:name|string',
            "lastName" => 'required_without:name|string',
            "address" => 'sometimes|string',
            "address2" => 'sometimes|string',
            "city" => 'sometimes|string',
            "state" => 'sometimes|string|size:2',
            "postalCode" => 'sometimes|string',
            "country" => 'sometimes|string',
            "phone" => 'sometimes|string|max:19',
            "email" => 'required|email'
        ];
    }

    public function toArray(): array
    {
        return array_filter(ObjectHelper::convertToArray($this));
    }
}
