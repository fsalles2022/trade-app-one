<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use NextelBR\Enumerators\NextelBRConstants;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

$factory->define(PointOfSale::class, function (Faker $faker) {

    $faker->addProvider(new pt_BR\Company($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    $label   = $faker->name;
    $company = $faker->company;

    return [
        'slug'         => str_slug($label),
        'label'        => $label,
        'cnpj'         => $faker->cnpj(false),
        'tradingName'  => $company,
        'companyName'  => $company . ' ' . $faker->companySuffix,
        'telephone'    => $faker->cellphone,
        'areaCode'     => $faker->areaCode,
        'zipCode'      => $faker->postcode,
        'local'        => $faker->streetName,
        'neighborhood' => $faker->citySuffix,
        'state'        => 'SP',
        'number'       => $faker->buildingNumber,
        'city'         => $faker->city,
        'complement'   => $faker->secondaryAddress,
        'latitude'     => $faker->latitude,
        'longitude'    => $faker->longitude
    ];
});

$factory->state(PointOfSale::class, 'with_identifiers', function () {
    return [
        "providerIdentifiers" => json_encode([
            Operations::CLARO => "15F0",
            Operations::TIM => "SP10_MGNORI_VA1880_AO11",
            Operations::OI => "1035863",
            Operations::NEXTEL => [
                NextelBRConstants::POINT_OF_SALE_COD => "787489",
                NextelBRConstants::POINT_OF_SALE_REF => "54154"
            ]
        ])
    ];
});

$factory->state(PointOfSale::class, 'fixed_slug', function () {
    return ['slug' => 'ACME'];
});

$factory->state(PointOfSale::class, 'label_too_long', function () {
    return ['label' => str_repeat('a', 256)];
});

$factory->state(PointOfSale::class, 'cnpj_too_long', function () {
    return ['cnpj' => 123456789012345];
});

$factory->state(PointOfSale::class, 'state_too_long', function () {
    return ['state' => 'SPA'];
});

$factory->state(PointOfSale::class, 'invalid_cnpj', function () {
    return ['cnpj' => '63605761000198'];
});

$factory->state(PointOfSale::class, 'tradingName_too_long', function () {
    return ['tradingName' => str_repeat('1', 256)];
});

$factory->state(PointOfSale::class, 'companyName_too_long', function () {
    return ['companyName' => str_repeat('1', 256)];
});

$factory->state(PointOfSale::class, 'network_too_long', function () {
    return ['network' => str_repeat('1', 256)];
});

$factory->state(PointOfSale::class, 'telephone_too_long', function () {
    return ['telephone' => 679992299992];
});

$factory->state(PointOfSale::class, 'network_id_invalid', function () {
    return ['networkId' => 2];
});

$factory->state(PointOfSale::class, 'network_empty', function () {
    return ['network' => []];
});

$factory->state(PointOfSale::class, 'network_null', function () {
    return ['network' => null];
});

$factory->state(PointOfSale::class, 'with_network', function () {
    return ['network' => ['id']];
});

$factory->state(PointOfSale::class, 'same_network', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Company($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    $network = [
        "name"      => "Queirós S.A.",
        "slug"      => "queiros-sa",
        "updatedAt" => "2018-02-05 15:35:14",
        "createdAt" => "2018-02-05 15:35:14",
        "id"       => "5a7879b2a281c2097b78e9bd"
    ];

    return [
        'network' => $network,
    ];
});
