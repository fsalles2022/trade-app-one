<?php

namespace Discount\Services;

use Carbon\Carbon;
use Discount\Enumerators\DiscountModes;
use Discount\Enumerators\DiscountStatus;
use Discount\Exceptions\DiscountExceptions;
use Discount\Models\Discount;
use Discount\Repositories\DeviceDiscountRepository;
use Discount\Repositories\DiscountRepository;
use Elastica\Aggregation\Filters;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Outsourced\Assistance\OutsourcedFactory;
use Outsourced\Enums\Crafts;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Exceptions\BuildExceptions;

class TriangulationWriteService
{
    protected $hierarchyService;
    protected $discountRepository;
    protected $productService;
    protected $deviceDiscountRepository;
    protected $discountService;

    public function __construct(
        DiscountRepository $discountRepository,
        HierarchyService $hierarchyService,
        ProductService $productService,
        DeviceDiscountRepository $deviceDiscountRepository,
        DiscountService $discountService
    ) {
        $this->discountRepository       = $discountRepository;
        $this->hierarchyService         = $hierarchyService;
        $this->productService           = $productService;
        $this->deviceDiscountRepository = $deviceDiscountRepository;
        $this->discountService          = $discountService;
    }

    public function create(User $user, array $attributes): Discount
    {
        $pointsOfSale = $this->getPointsOfSaleFilterdByMode($user, $attributes);
        $this->notExistsDiscountWithDevice($pointsOfSale, $attributes);

        $adapted  = $this->adapterDiscount($user, $attributes);
        $discount = $this->discountRepository->create($adapted);

        $this->productService->create($user, $discount, $attributes);
        $this->attachDevicesInDiscount($attributes, $discount);
        $this->attachPointsOfSalesInDiscount($discount, $pointsOfSale, $attributes);

        $network = $user->getNetwork()->slug;

        if (OutsourcedFactory::hasIntegration($network, Crafts::TRIANGULATION)) {
            $outsourced             = OutsourcedFactory::make($network, Crafts::TRIANGULATION, false);
            $attributes['discount'] = $discount;
            $outsourced->processCustomDataFromTriangulation($attributes);
        }

        return $discount;
    }

    public function update(User $user, int $id, array $attributes): Discount
    {
        $discount = $this->discountRepository->findById($id);
        $this->hasPermissionUnderTriangulation($user, $discount);

        $devices  = data_get($attributes, 'devices');
        $mode     = data_get($attributes, 'filterMode');
        $cnpjs    = data_get($attributes, 'pointsOfSale', []);
        $products = data_get($attributes, 'products', []);

        $pointsOfSale = $this->getPointOfSalesBasedInFilterMode($user, $cnpjs, $mode);

        $adapted = $this->adapterDiscount($user, $attributes);

        $products && $this->productService->update($user, $discount, $products);
        $devices && $this->attachDevicesInDiscount($attributes, $discount);
        $pointsOfSale && $this->attachPointsOfSalesInDiscount($discount, $pointsOfSale, $attributes);

        $network = $user->getNetwork()->slug;

        if (OutsourcedFactory::hasIntegration($network, Crafts::TRIANGULATION)) {
            $outsourced             = OutsourcedFactory::make($network, Crafts::TRIANGULATION, false);
            $attributes['discount'] = $discount;
            $outsourced->processUpdateFromTriangulation($attributes);
        }

        return $this->discountRepository->update($discount, $adapted);
    }

    /** @param mixed[] $attributes */
    public function switchStatus(User $user, int $id, array $attributes): Discount
    {
        $discount = $this->discountRepository->findById($id);
        $this->hasPermissionUnderTriangulation($user, $discount);

        $status = data_get($attributes, 'status', DiscountStatus::INACTIVE);

        return $this->discountRepository->update($discount, ['status' => $status]);
    }

    /** @param mixed[] $attributes */
    public function changeDates(User $user, array $request, string $startAt, string $endAt)
    {
        $discounts = $this->getDiscountsByFilters($user, $request['ids'], $request['filters']);

        $attributes = [
            'startAt' => Carbon::make($startAt)->startOfDay()->format('Y-m-d H:i:s'),
            'endAt' => Carbon::make($endAt)->startOfDay()->format('Y-m-d H:i:s')
        ];

        $processedDiscounts = [];

        DB::beginTransaction();
        
        foreach ($discounts as $singleDiscount) {
            try {
                $pointsOfSale = $this->getPointsOfSaleFilterdByMode($user, $attributes);
                
                $attributes['devices'] = [
                    ['ids' => $singleDiscount->devices()->get()->pluck('id')->toArray()]
                ];
                
                $attributes ['pointsOfSale'] = $singleDiscount->pointsOfSale()->get();

                $attributes ['products'] = $singleDiscount->products()->get();

                $pointsOfSale = $this->getPointsOfSaleFilterdByMode($user, $attributes);

                $this->notExistsDiscountWithDevice($pointsOfSale, $attributes);
                
                $this->hasPermissionUnderTriangulation($user, $singleDiscount);
                
                $this->discountRepository->update($singleDiscount, $attributes);

                array_push($processedDiscounts, $singleDiscount);
            } catch (BuildExceptions $exception) {
                DB::rollback();

                $exceptionMessage = $exception->getMessage();

                $discountName = $singleDiscount->title;

                throw DiscountExceptions::errorInChangingDiscountDate($discountName, $exceptionMessage);
            } catch (\Throwable $error) {
                DB::rollback();

                throw $error;
            }
        }
        DB::commit();

        return $processedDiscounts;
    }

    private function getPointOfSalesBasedInFilterMode(User $user, array $cnpjs, ?string $mode)
    {
        $pointsOfSaleAuthorized = $this->hierarchyService
            ->getPointsOfSaleThatBelongsToUser($user);
        $pointsOfSaleToAttach   = collect();

        if ($mode == DiscountModes::CHOSEN) {
            $pointsOfSale         = $pointsOfSaleAuthorized->whereIn('cnpj', $cnpjs);
            $pointsOfSaleToAttach = $pointsOfSaleToAttach->merge($pointsOfSale);
        }

        if ($mode == DiscountModes::ALL) {
            $pointsOfSaleToAttach = $pointsOfSaleAuthorized;
        }

        return $pointsOfSaleToAttach;
    }

    private function getPointsOfSaleFilterdByMode(User $user, array $attributes): Collection
    {
        $cnpjs      = data_get($attributes, 'pointsOfSale', []);
        $chosenMode = data_get($attributes, 'filterMode') === DiscountModes::CHOSEN;

        return ($chosenMode)
            ? $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user)->whereIn('cnpj', $cnpjs)
            : $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);
    }

    private function adapterDiscount(User $user, array $attributes): array
    {
        $endAt = data_get($attributes, 'endAt');

        return array_filter([
            'title' => data_get($attributes, 'title'),
            'status' => data_get($attributes, 'status', DiscountStatus::ACTIVE),
            'filterMode' => data_get($attributes, 'filterMode'),
            'startAt' => data_get($attributes, 'startAt'),
            'endAt' => $endAt ? Carbon::parse($endAt)->endOfDay() : null,
            'userId' => $user->id,
            'networkId' => $user->getNetwork()->id
        ]);
    }

    public function notExistsDiscountWithDevice(Collection $pointsOfSale, array $attributes): bool
    {
        $products   = data_get($attributes, 'products', []);
        $startAt    = data_get($attributes, 'startAt');
        $devices    = data_get($attributes, 'devices', []);
        $devicesIds = array_merge(...array_pluck($devices, 'ids'));

        $operators  = array_pluck($products, 'operator');
        $operations = Arr::collapse(array_pluck($products, 'operations'));

        $discounts = $this->discountRepository
            ->findByDevicesAndPointsOfSale($devicesIds, $startAt, $operators, $operations);

        $cnpjs = $pointsOfSale->pluck('id')->toArray();

        $discountsFiltered = $discounts->filter(static function (Discount $discount) use ($cnpjs) {
            return ($discount->filterMode === DiscountModes::ALL)
                ? true
                : $discount->pointsOfSale->whereIn('id', $cnpjs)->isNotEmpty();
        });

        $haveSamePlanOrPromotion = $this->checkPlansTriangulation($discountsFiltered, $attributes);

        if ($haveSamePlanOrPromotion === false || $discountsFiltered->isEmpty()) {
            return true;
        }

        $hasDiscount = $discounts->first()
            ->devices->whereIn('device.id', $devicesIds)->pluck('device.label')->first();

        throw DiscountExceptions::deviceAlreadyHasDiscountForPointOfSale($hasDiscount);
    }

    private function attachDevicesInDiscount(array $attributes, Discount $discount): void
    {
        $devices = data_get($attributes, 'devices', []);
        $this->deviceDiscountRepository->deleteByDiscount($discount->id);
        foreach ($devices as $device) {
            $ids = data_get($device, 'ids');
            foreach ($ids as $id) {
                $device['deviceId']   = $id;
                $device['discountId'] = $discount->id;
                $this->deviceDiscountRepository->create($device);
            }
        }
    }

    private function attachPointsOfSalesInDiscount(Discount $discount, Collection $pointsOfSale, $attributes): void
    {
        if (data_get($attributes, 'filterMode') === DiscountModes::ALL) {
            $pointsOfSale = collect();
        }

        $discount->pointsOfSale()->sync($pointsOfSale->pluck('id'));
    }

    public function delete(int $id, $user): bool
    {
        $discount = $this->discountRepository->findById($id);
        $this->hasPermissionUnderTriangulation($user, $discount);

        return $discount->delete();
    }

    public function hasPermissionUnderTriangulation($user, Discount $triangulation): bool
    {
        $triangulations = $this->discountRepository->triangulationsAuthorized($user);
        $authorization  = $triangulations->where('id', '=', $triangulation->id);

        if ($authorization->isNotEmpty()) {
            return true;
        }

        throw DiscountExceptions::userHasNotAuthorizationUnderTriangulation();
    }

    private function checkPlansTriangulation(Collection $discountsFiltered, array $attributes): bool
    {
        $products                  = data_get($attributes, 'products', []);
        $occursSamePlanOrPromotion = false;
        $plans                     = array_pluck($products, 'plans');
        $flattenPlans              = collect($plans)->flatten(1);

        $startAt = data_get($attributes, 'startAt');
        $endAt   = data_get($attributes, 'endAt');

        $promotions    = array_pluck($products, 'promotions');
        $promotionsIds = collect($promotions)->flatten(1)->pluck('id');

        $discountsFiltered->each(function (Discount $discount) use (&$occursSamePlanOrPromotion, $promotionsIds, $flattenPlans, $startAt, $endAt) {
            $discount->products->each(function ($product) use (&$occursSamePlanOrPromotion, $promotionsIds, $flattenPlans, $startAt, $endAt, $discount) {
                $productPromotion = data_get($product, 'promotion', null);
                if ($productPromotion &&
                    $this->checkStartAtAndEndAtTriangulation($startAt, $endAt, $discount->startAt, $discount->endAt) &&
                    in_array($productPromotion, $promotionsIds->toArray())
                ) {
                    $occursSamePlanOrPromotion = true;
                    return false;
                }

                if ($productPromotion === null && $promotionsIds->isEmpty()) {
                    $plansId    = $flattenPlans->pluck('id');
                    $operators  = $flattenPlans->pluck('operator')->unique('operator');
                    $operations = $flattenPlans->pluck('operation')->unique('operation');
                    if ($this->checkStartAtAndEndAtTriangulation($startAt, $endAt, $discount->startAt, $discount->endAt) &&
                        in_array($product->product, $plansId->toArray(), true) &&
                        in_array($product->operator, $operators->toArray(), true) &&
                        in_array($product->operation, $operations->toArray(), true)
                    ) {
                        $occursSamePlanOrPromotion = true;
                        return false;
                    }
                }
            });
        });

        return $occursSamePlanOrPromotion;
    }

    private function checkStartAtAndEndAtTriangulation($startAt, $endAt, $discountStartAt, $discountEndAt): bool
    {
        return Carbon::parse($startAt)->startOfDay() <= $discountStartAt && Carbon::parse($endAt)->endOfDay() <= $discountEndAt;
    }

    private function getDiscountsByFilters(User $user, array $ids, ?array $filters): Collection
    {
        if ($ids === []) {
            $items     = $this->discountService->filter($user, $filters);
            $discounts = $items->get();
            return $discounts;
        }

        $discounts = $this->discountRepository->findMany($ids);

        return $discounts;
    }
}
