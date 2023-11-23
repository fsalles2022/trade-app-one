<?php

namespace Buyback\Providers\Faker;

use Faker\Provider\Base;

class Smartphone extends Base
{
    protected $brand;
    protected static $brandModelNames = array(
        'Apple' => array(
            'iPhone XR', 'iPhone XS Max', 'iPhone XS', 'Phone X', 'iPhone 8 Plus', 'iPhone 8', 'iPhone 7 Plus',
            'iPhone 7', 'iPhone SE', 'iPhone 6S Plus', 'iPhone 6S', 'iPhone 6 Plus', 'iPhone 6', 'iPhone 5S',
            'iPhone 5C', 'iPhone 5', 'iPhone 4S', 'iPhone 4', 'iPhone 3GS', 'iPhone 3G', 'iPhone'
        ),
        'Motorola' => array(
            'Moto E5 Play Go', 'Moto Z3 Play', 'Moto E5 Plus', 'Moto E5', 'Moto G6 Play', 'Moto G6 Plus',
            'Moto G6', 'Moto X4', 'Moto G5s Plus', 'Moto G5S', 'Moto Z2 Force', 'Moto E4 Plus', 'Moto E4',
            'Moto Z2 Play', 'Moto C', 'Moto C Plus', 'Moto G5 Plus', 'Moto G5', 'Moto M', 'Moto Z Play',
            'Moto E3 Power', 'Moto E3', 'Moto Z', 'Moto Z Force'
        ),
        'Samsung' => array(
            'Galaxy S9', 'Galaxy S9+', 'Galaxy SLite Luxury', 'Galaxy S8', 'Galaxy S8+', 'Galaxy S8 Active',
            'Galaxy S7', 'Galaxy S7 Edge', 'Galaxy S6 Edge+', 'Galaxy S6 Active', 'Galaxy S5 Neo', 'Galaxy S6',
            'Galaxy J4 Core', 'Galaxy J6+', 'Galaxy J4+', 'Galaxy J2 Core', 'Galaxy J7', 'Galaxy J8',
            'Galaxy J3', 'Galaxy J6', 'Galaxy J4', 'Galaxy J7', 'Galaxy J7 Prime 2', 'Galaxy J2 Pro'
        )
    );

    /**
     * @example 'iPhone XR'
     */
    public function model()
    {
        $brandNames  = array_keys(static::$brandModelNames);
        $this->brand = static::randomElement($brandNames);
        $model       = static::randomElementByKey(static::$brandModelNames, $this->brand);
        return $model;
    }
    /**
     * @example 'Apple'
     */
    public function brand()
    {
        $brandNames = array_keys(static::$brandModelNames);
        return $this->brand ?? static::randomElement($brandNames);
    }

    protected static function randomElementByKey($array, $key)
    {
        if (! $array || empty($array[$key])) {
            return null;
        }
        return static::randomElement($array[$key]);
    }
}
