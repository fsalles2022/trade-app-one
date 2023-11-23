<?php

use Discount\Models\DiscountProduct;
use Faker\Generator as Faker;
use NextelBR\Services\NextelBRMapPlansService;
use TradeAppOne\Domain\Enumerators\Operations;

$factory->define(DiscountProduct::class, function (Faker $faker) {
    $selectedSector    = Operations::TELECOMMUNICATION_OPERATORS;
    $selectedOperator  = $faker->randomElement(array_keys($selectedSector));
    $selectedOperation = $faker->randomElement(array_keys($selectedSector[$selectedOperator]));
    $nextelPlans       = json_decode(file_get_contents(base_path() . '/modules/nextelbr/tests/ServerTest/responses/plans/successPlans.json'),
        true)['planos'];
    $oiBoletoPlans     = json_decode(file_get_contents(__DIR__ . '/../../modules/oibr/tests/ServerTest/responses/plans/successControleBoletoPlans.json'),
        true);
    $oiCartaoPlans     = json_decode(file_get_contents(__DIR__ . '/../../modules/oibr/tests/ServerTest/responses/plans/successControleCartaoPlans'),
        true);

    $timPlans = json_decode(file_get_contents(__DIR__ . '/../../modules/timbr/tests/ServerTest/controleFaturaEligibilitySucess.json'),
        true)['products'];

    switch ($selectedOperation) {
        case Operations::OI_CONTROLE_BOLETO:
            $plan    = \OiBR\Services\OiBRMapPlansService::map($oiBoletoPlans)
                ->shuffle()
                ->first();
            $product = $plan['product'];
            $label   = $plan['label'];
            $price   = $plan['price'];
            break;
        case Operations::OI_CONTROLE_CARTAO:
            $plan    = \OiBR\Services\OiBRMapPlansService::map($oiCartaoPlans)
                ->shuffle()
                ->first();
            $product = $plan['product'];
            $label   = $plan['label'];
            $price   = $plan['price'];
            break;
        case Operations::NEXTEL_CONTROLE_CARTAO:
            $plan    = NextelBRMapPlansService::map($nextelPlans,
                ['operation' => Operations::NEXTEL_CONTROLE_CARTAO])->shuffle()->first();
            $product = $plan['product'];
            $label   = $plan['label'];
            $price   = $plan['price'];
            break;
        case Operations::NEXTEL_CONTROLE_BOLETO:
            $plan    = NextelBRMapPlansService::map($nextelPlans,
                ['operation' => Operations::NEXTEL_CONTROLE_BOLETO])->shuffle()->first();
            $product = $plan['product'];
            $label   = $plan['label'];
            $price   = $plan['price'];
            break;
        case Operations::TIM_CONTROLE_FATURA:
            $plan    = \TimBR\Services\TimBRMapPlansService::map($timPlans,
                Operations::TIM_CONTROLE_FATURA)
                ->shuffle()
                ->first();
            $product = $plan['product'];
            $label   = $plan['label'];
            $price   = $plan['price'];
            break;
        case Operations::TIM_EXPRESS:
            $plan    = \TimBR\Services\TimBRMapPlansService::map($timPlans,
                Operations::TIM_EXPRESS)
                ->shuffle()
                ->first();
            $product = $plan['product'];
            $label   = $plan['label'];
            $price   = $plan['price'];
            break;
        case Operations::VIVO_CONTROLE:
            $product = $faker->randomElement(\VivoBR\Tests\ServerTest\SunTestBook::BOLETO_PRODUCTS);
            $label   = $faker->sentence;
            $price   = $faker->randomFloat(2, 0, 100);
            break;
        case Operations::VIVO_CONTROLE_CARTAO:
            $product = $faker->randomElement(\VivoBR\Tests\ServerTest\SunTestBook::CARTAO_PRODUCTS);
            $label   = $faker->sentence;
            $price   = $faker->randomFloat(2, 0, 100);
            break;
        default:
            $product = str_random(6);
            $label   = $faker->sentence;
            $price   = $faker->randomFloat(2, 0, 100);
            break;
    }

    return [
        'operator'   => $selectedOperator,
        'operation'  => $selectedOperation,
        'product'    => $product,
        'filterMode' => 'ALL',
        'label'      => $label,
    ];
});