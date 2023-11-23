<?php


namespace Generali\Adapters\Request;

use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Models\Collections\Service;

class GeneraliActivationRequestAdapter implements RequestAdapterBehavior
{
    public const CPF = 'CPF';

    public static function adapt(Service $service, $extra = null): array
    {
        return [
            'customer'    => self::customer($service),
            'service'     => self::service($service),
            'payment'     => self::payment($service),
            'pointOfSale' => self::pointOfSale($service)
        ];
    }

    private static function customer(Service $service): array
    {
        $customer = data_get($service, 'customer');

        return [
            'firstName' => data_get($customer, 'firstName'),
            'lastName'  => data_get($customer, 'lastName'),
            'birthday'  => data_get($customer, 'birthday'),
            'mainPhone' => data_get($customer, 'mainPhone'),
            'gender'    => data_get($customer, 'gender'),
            'document' => [
                'type'   => self::CPF,
                'number' => data_get($customer, 'cpf')
            ],
            'email'   => data_get($customer, 'email'),
            'address' => self::address($customer)
        ];
    }

    private static function address(array $customer): array
    {
        return [
            'number'       => data_get($customer, 'number'),
            'zipCode'      => data_get($customer, 'zipCode'),
            'state'        => data_get($customer, 'state'),
            'city'         => data_get($customer, 'city'),
            'local'        => data_get($customer, 'local'),
            'neighborhood' => data_get($customer, 'neighborhood'),
            'complement'   => data_get($customer, 'complement')
        ];
    }

    private static function service(Service $service): array
    {
        $device = data_get($service, 'device');

        return [
            'device' => [
                'imei'  => data_get($device, 'imei'),
                'brand' => data_get($device, 'brand'),
                'date' => data_get($device, 'date'),
                'model' => data_get($device, 'model'),
                'price' => data_get($device, 'price'),
                'warrantyManufacturer' => data_get($device, 'warrantyManufacturer'),
            ],
            'product'   => data_get($service, 'product'),
            'reference' => data_get($service, 'serviceTransaction')
        ];
    }

    private static function payment(Service $service): array
    {
        return [
            'times'  => data_get($service, 'payment.times'),
            'status' => data_get($service, 'payment.status')
        ];
    }

    private static function pointOfSale(Service $service): array
    {
        $pointOfSale = $service->sale->pointOfSale;
        return [
            "cnpj" => data_get($pointOfSale, 'cnpj'),
            "slug" => data_get($pointOfSale, 'slug'),
            "network" => [
                "slug" => data_get($pointOfSale, 'network.slug')
            ]
        ];
    }
}
