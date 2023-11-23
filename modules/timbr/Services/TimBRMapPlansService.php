<?php

namespace TimBR\Services;

use Discount\Models\DeviceTim;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use TimBR\Enumerators\TimBRPackages;
use TimBR\Enumerators\TimBRSegments;
use TimBR\Enumerators\TimBRServices;
use TradeAppOne\Domain\Enumerators\Operations;

class TimBRMapPlansService
{
    public const LOYALTY_DEVICE_TYPE  = 'Aparelho';
    public const LOYALTY_PRODUCT_TYPE = 'Produto';
    public const CONTROLE_FATURA_PRODUCT_ID_IGNORE = [
        '1-IL65OD'
    ];

    public static function map(array $products, string $operation = '', ?bool $requireDeviceLoyalty = null, ?DeviceTim $device = null): Collection
    {
        $collectionOfPlans     = new Collection();
        $filteredPlans         = self::getProductsFiltered($products, $operation, $requireDeviceLoyalty);
        $newProduct            = [];
        $operationsToTransform = array_flip(TimBRSegments::TRANSLATE);
        $deviceProducts        = self::getDeviceProductsFiltered($device);

        foreach ($filteredPlans as $product) {
            $productSegment        = data_get($product, 'plan.segment');
            $productId             = data_get($product, 'plan.id');
            $productLabel          = data_get($product, 'plan.name');
            $productDescription    = data_get($product, 'plan.description');
            $price                 = (float) data_get($product, 'plan.price.amount', 0);
            $newProduct['product'] = $productId;
            $newProduct['label']   = data_get($product, 'plan.name');
            $newProduct['price']   = $price;

            if (array_key_exists($productSegment, $operationsToTransform)) {
                $newProduct['operation'] = $operationsToTransform[$productSegment];
            }

            $details = [];

            if (! empty($productDescription)) {
                $details[] = $productDescription;
            }

            foreach (data_get($product, 'plan.benefits', []) as $benefit) {
                $newDetails = Str::ucfirst(trim(data_get($benefit, 'quantity')) . ' - ' . data_get(
                    $benefit,
                    'description'
                ));
                $details[]  = ucfirst($newDetails);
            }

            $packages = self::getPackages($product);
            $services = self::getServices($product);

            $newProduct['packages'] = $packages;
            $newProduct['services'] = $services;
            $newProduct['details']  = $details;
            $newProduct['tim']      = $product;

            if (self::shouldIncludePlanWithoutLoyalty($product)) {
                // Without loyalty product
                $collectionOfPlans->push($newProduct);
            }

            // Loyalties Products
            if ($loyalties = data_get($product, 'loyalties')) {
                $planLoyalty   = null;
                $deviceLoyalty = null;

                // Mount single Device Loyalty and single plan Loyalty
                foreach ($loyalties as $loyalty) {
                    $newSubProduct            = [];
                    $loyaltyPrice             = (float) data_get($loyalty, 'price.amount');
                    $loyaltyLabel             = isset($loyalty['description']) && ! empty($loyalty['description']) ? data_get($loyalty, 'description') : data_get($loyalty, 'name');
                    $newSubProduct['product'] = $productId;
                    $newSubProduct['loyalty'] = [
                        'id'    => data_get($loyalty, 'id'),
                        'price' => $loyaltyPrice,
                        'label' => data_get($loyalty, 'description'),
                        'loyalties' => [
                            [
                                'id'    => data_get($loyalty, 'id'),
                                'price' => $loyaltyPrice,
                                'label' => $loyaltyLabel,
                                'type'  => data_get($loyalty, 'type'),
                            ]
                        ]
                    ];
                    $newSubProduct['price']   = $price + $loyaltyPrice;
                    $newSubProduct['packages'] = $packages;
                    $newSubProduct['services'] = $services;
                    $newSubProduct['details'] = $details;
                    $newSubProduct['tim']     = $product;

                    // Ignore Device loyalty when product is not include in available products list for device
                    if (data_get($loyalty, 'type') === self::LOYALTY_DEVICE_TYPE) {
                        if (in_array($productId, $deviceProducts) === false) {
                            continue;
                        }

                        $deviceLoyalty = $loyalty;

                        $newSubProduct['label'] = $productLabel . ' - ' . trans('timBR::messages.eligibility.device_loyalty');

                        $collectionOfPlans->push($newSubProduct);

                        continue;
                    }

                    $planLoyalty = $loyalty;

                    $newSubProduct['label'] = $productLabel . ' - ' . trans('timBR::messages.eligibility.plan_loyalty');

                    $collectionOfPlans->push($newSubProduct);
                }

                // Mount union of Device Loyalty and plan Loyalty
                if ($planLoyalty !== null && $deviceLoyalty !== null) {
                    $newSubProduct            = [];
                    $deviceLoyaltyPrice       = (float) data_get($deviceLoyalty, 'price.amount');
                    $planLoyaltyPrice         = (float) data_get($planLoyalty, 'price.amount');
                    $newSubProduct['product'] = $productId;
                    $newSubProduct['loyalty'] = [
                        'id'    => data_get($deviceLoyalty, 'id') . '|' . data_get($planLoyalty, 'id'),
                        'price' => $planLoyaltyPrice + $deviceLoyaltyPrice,
                        'label' => data_get($planLoyalty, 'description') . ' ' . data_get($deviceLoyalty, 'description'),
                        'loyalties' => [
                            [
                                'id'    => data_get($deviceLoyalty, 'id'),
                                'price' => $deviceLoyaltyPrice,
                                'label' => data_get($deviceLoyalty, 'description'),
                                'type'  => data_get($deviceLoyalty, 'type'),
                            ],
                            [
                                'id'    => data_get($planLoyalty, 'id'),
                                'price' => $planLoyaltyPrice,
                                'label' => data_get($planLoyalty, 'description'),
                                'type'  => data_get($planLoyalty, 'type'),
                            ],
                        ]
                    ];

                    $newSubProduct['label']   = $productLabel . ' - ' . trans('timBR::messages.eligibility.plan_device_loyalty');
                    $newSubProduct['price']   = $price + $planLoyaltyPrice + $deviceLoyaltyPrice;
                    $newSubProduct['packages'] = $packages;
                    $newSubProduct['services'] = $services;
                    $newSubProduct['details'] = $details;
                    $newSubProduct['tim']     = $product;

                    $collectionOfPlans->push($newSubProduct);
                }
            }
        }

        // Return only products with loyalty device
        // Products without loyalty or only plan loyalty are removed
        if ($requireDeviceLoyalty === true) {
            return $collectionOfPlans->filter(function ($plan) {
                return collect(data_get($plan, 'loyalty.loyalties', []))
                    ->where('type', '=', self::LOYALTY_DEVICE_TYPE)
                    ->isNotEmpty();
            })->values();
        }

        return $collectionOfPlans;
    }

    private static function getProductsFiltered(array $products, string $operation = '', ?bool $requireDeviceLoyalty = null): Collection
    {
        $translatedSegment = TimBRSegments::TRANSLATE[$operation] ?? '';

        $products         = collect($products)->where('plan.segment', $translatedSegment);
        $productsFiltered = collect([]);

        foreach ($products as $product) {
            $productPrice = (float) data_get($product, 'plan.price.amount', 0);
            $productId = data_get($product, 'plan.id');

            // Skip specify products when Controle Fatura
            if ($operation === Operations::TIM_CONTROLE_FATURA && in_array($productId, self::CONTROLE_FATURA_PRODUCT_ID_IGNORE)) {
                continue;
            }

            // Skip Dependents products
            if ($operation === Operations::TIM_BLACK_MULTI && $productPrice === 0.0) {
                continue;
            }

            // Dependent products
            if ($operation === Operations::TIM_BLACK_MULTI_DEPENDENT) {
                if ($productPrice > 0.0) {
                    continue;
                }

                $productsFiltered->push($product);

                continue;
            }

            // Skip Loyalty Filters
            if ($requireDeviceLoyalty === null) {
                $productsFiltered->push($product);

                continue;
            }

            $loyalties = collect(data_get($product, 'loyalties', []));

            // Remove Loyalty Device offer
            if ($requireDeviceLoyalty === false) {
                $product['loyalties'] = $loyalties->where('type', '!=', self::LOYALTY_DEVICE_TYPE);

                $productsFiltered->push($product);

                continue;
            }

            $deviceLoyalty = $loyalties->where('type', '=', self::LOYALTY_DEVICE_TYPE);

            // Skip Products without loyalty device
            if ($deviceLoyalty->isEmpty()) {
                continue;
            }

            $productsFiltered->push($product);
        }

        return $productsFiltered;
    }

    protected static function getDeviceProductsFiltered(?DeviceTim $deviceTim = null): array
    {
        if ($deviceTim === null) {
            return [];
        }

        $deviceTim->load('products');

        return $deviceTim
            ->products
            ->where('pivot.discount', '>', 0)
            ->pluck('externalIdentifier')
            ->values()
            ->toArray();
    }

    /**
     * @param mixed[] $product
     * @return mixed[]|null
     */
    protected static function getServices(array $product): ?array
    {
        $services = collect(data_get($product, 'serviceGroups.groups.*.services.*', []));

        if ($services->isEmpty()) {
            return null;
        }

        return $services->map(function ($service): array {
            $id = data_get($service, 'id');
            $type = in_array($id, TimBRServices::DEEZER_SERVICES) ? TimBRServices::DEEZER_TYPE : TimBRServices::PLUGIN_TYPE;

            return [
                'id'          => $id,
                'label'       => data_get($service, 'name'),
                'description' => data_get($service, 'description'),
                'price'       => (float) data_get($service, 'price.amount', 0),
                'type'        => $type,
            ];
        })
            ->values()
            ->toArray();
    }

    /**
     * @param mixed[] $product
     * @return mixed[]|null
     */
    protected static function getPackages(array $product): ?array
    {
        $packages = collect(data_get($product, 'packageGroups.groups.*.packages.*', []))
            ->whereIn('id', TimBRPackages::AVAILABLE_PACKAGES);

        if ($packages->isEmpty()) {
            return null;
        }

        return $packages->map(function ($package): array {
            $type = array_flip(TimBRPackages::EXTERNAL_IDENTIFIERS_BY_TYPE)[data_get($package, 'id')] ?? '';

            return [
                'id'          => data_get($package, 'id'),
                'label'       => data_get($package, 'name'),
                'description' => data_get($package, 'description'),
                'price'       => (float) data_get($package, 'price.amount', 0),
                'type'        => $type,
            ];
        })
            ->values()
            ->toArray();
    }

    /** @param mixed[] $product */
    protected static function shouldIncludePlanWithoutLoyalty(array $product): bool
    {
        /** @var Collection $loyalties */
        $loyalties = data_get($product, 'loyalties');

        if (empty($loyalties) || ($loyalties instanceof Collection && $loyalties->isEmpty())) {
            return true;
        }

        foreach ($loyalties as $loyalty) {
            $requiredProduct = $loyalty['requiredProduct'] ?? false;

            // Bool as string is returned in Eligibility by TIM
            if ($requiredProduct === 'true') {
                return false;
            }
        }

        return true;
    }
}
