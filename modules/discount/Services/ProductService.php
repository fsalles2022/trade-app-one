<?php

namespace Discount\Services;

use Discount\Adapters\ProductAdapter;
use Discount\Enumerators\DiscountModes;
use Discount\Models\Discount;
use Discount\Repositories\ProductRepository;
use Illuminate\Support\Collection;
use NextelBR\Services\NextelBRService;
use OiBR\Assistance\OiBRService;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\AuthService;
use TradeAppOne\Domain\Services\HierarchyService;
use VivoBR\Services\VivoBRService;

class ProductService
{
    protected $oiBRService;
    protected $nextelBRService;
    protected $vivoBRService;
    protected $authService;
    protected $hierarchyService;
    protected $productRepository;

    public function __construct(
        HierarchyService $hierarchyService,
        AuthService $authService,
        OiBRService $oiBRService,
        NextelBRService $nextelBRService,
        VivoBRService $vivoBRService,
        ProductRepository $productRepository
    ) {
        $this->authService       = $authService;
        $this->oiBRService       = $oiBRService;
        $this->nextelBRService   = $nextelBRService;
        $this->vivoBRService     = $vivoBRService;
        $this->hierarchyService  = $hierarchyService;
        $this->productRepository = $productRepository;
    }

    public function availableServices(User $user)
    {
        return $this->authService->loadAvailableServices($user)['availableServices'];
    }

    public function filter(User $user, $operators = []): Collection
    {
        $availableOperators = $this->availableServices($user)['LINE_ACTIVATION'];
        return ProductAdapter::adapt(collect($availableOperators), $user, $operators);
    }

    public function create(User $user, Discount $discount, array $products): void
    {
        $operators           = array_column($products, 'operator');
        $productsAdapted     = $this->filter($user, $operators);
        $plansAvailable      = $productsAdapted->get('products');
        $promotionsAvailable = $productsAdapted->get('promotions');
        $productsRequest     = data_get($products, 'products', $products);
        foreach ($productsRequest as $product) {
            $plans      = (data_get($product, 'plans')) ? : [];
            $operations = (data_get($product, 'operations')) ? : [];
            $promotions = data_get($product, 'promotions', []);
            if (empty($plans)) {
                $plainProducts =array_map(static function ($operation) use ($product) {
                    $plan = [
                        'operator'  => data_get($product, 'operator'),
                        'operation' => $operation
                    ];
                    return $plan;
                }, $operations);
            } else {
                $plainProducts = array_map(static function ($plan) {
                    $plan['product'] = $plan['id'];
                    unset($plan['id'], $plan['label']);
                    return $plan;
                }, $plans);
            }
            foreach ($plainProducts as $plainProduct) {
                $productId          = data_get($plainProduct, 'product');
                $plan               = $plansAvailable->where('product', $productId)->first();
                $promotionsFiltered = $promotionsAvailable->where('product', $productId);

                $plainProduct['label']      = $productId ? data_get($plan, 'label') : null;
                $plainProduct['filterMode'] = $productId ? DiscountModes::CHOSEN : DiscountModes::ALL;
                $plainProduct['discountId'] = $discount->id;

                $productPromotions = array_filter($promotions, function ($promotion) use ($promotionsFiltered) {
                    return $promotionsFiltered->contains('id', $promotion['id'])
                        && $promotionsFiltered->contains('operation', $promotion['operation']);
                });
                if (empty($productPromotions)) {
                    $this->productRepository->create($plainProduct);
                } else {
                    $distinctPromotions = collect($productPromotions)->unique('id')->toArray();
                    foreach ($distinctPromotions as $promotion) {
                        $plainProduct['promotion'] = (string) $promotion['id'];
                        $this->productRepository->create($plainProduct);
                    }
                }
            }
        }
    }

    public function update(User $user, Discount $discount, $products)
    {
        $discountOld = $discount->products->pluck('id')->toArray();
        $this->productRepository->delete($discountOld);

        $this->create($user, $discount, $products);
    }
}
