<?php

namespace Discount\Http\Controllers;

use Discount\Adapters\DiscountAdapter;
use Discount\Http\Requests\DiscountFormRequest;
use Discount\Http\Resources\DiscountInSaleResource;
use Discount\Http\Resources\TriangulationSimulationResource;
use Discount\Models\Discount;
use Discount\Repositories\DeviceDiscountRepository;
use Discount\Services\DiscountService;
use Discount\Services\TriangulationWriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TradeAppOne\Domain\Enumerators\Permissions\TriangulationPermission;
use TradeAppOne\Domain\Services\DeviceService;
use TradeAppOne\Http\Controllers\Controller;

class DiscountController extends Controller
{
    private $discountService;
    private $writeService;
    private $deviceDiscountRepository;
    private $deviceService;

    public function __construct(
        DiscountService $discountService,
        TriangulationWriteService $writeService,
        DeviceDiscountRepository $deviceDiscountRepository,
        DeviceService $deviceService
    ) {
        $this->discountService          = $discountService;
        $this->writeService             = $writeService;
        $this->deviceDiscountRepository = $deviceDiscountRepository;
        $this->deviceService            = $deviceService;
    }

    public function getDiscount(int $id): Collection
    {
        return $this->discountService->getDiscountById($id);
    }

    public function discounts(DiscountFormRequest $request): JsonResponse
    {
        $discounts = $this->discountService
            ->filter($request->user(), $request->validated())
            ->paginate(10);

        $items = $discounts->getCollection()->map(static function ($discount) {
            return DiscountAdapter::adapt($discount);
        });

        $discounts->setCollection($items);

        return response()->json($discounts, Response::HTTP_OK);
    }

    public function update(DiscountFormRequest $request, $id)
    {
        hasPermissionOrAbort(TriangulationPermission::getFullName(TriangulationPermission::EDIT));

        $discount = $this->writeService
            ->update($request->user(), $id, $request->validated());

        return response()->json($discount, Response::HTTP_OK);
    }

    public function switchStatusAction(DiscountFormRequest $request, int $id): JsonResponse
    {
        $discount = $this->writeService->switchStatus($request->user(), $id, $request->validated());

        return response()->json($discount, Response::HTTP_OK);
    }

    public function changeDatesAction(DiscountFormRequest $request): JsonResponse
    {
        return response()->json(
            $this->writeService->changeDates($request->user(), $request->validated(), $request->startAt, $request->endAt),
            Response::HTTP_OK
        );
    }
    
    public function deviceWithDiscount(Request $request)
    {
        return $this->discountService->getDevicesWithDiscounts(Auth::user(), $request->all());
    }

    public function discountsInSale(DiscountFormRequest $request)
    {
        $received = $this->discountService
            ->triangulationInSale($request->user(), $request->validated());

        return response()->json(
            DiscountInSaleResource::toArray($received->triangulations, $received->setDevice, $received->hasIntegration),
            $received->status
        );
    }

    public function create(DiscountFormRequest $request): Discount
    {
        hasPermissionOrAbort(TriangulationPermission::getFullName(TriangulationPermission::CREATE));

        return $this->writeService->create($request->user(), array_filter($request->all()));
    }

    public function simulation(DiscountFormRequest $request): JsonResponse
    {
        $deviceId  = $request->get('deviceId');
        $discounts = $this->discountService
            ->triangulationsAvailableToPointOfSale($request->user(), ['devices' => $deviceId]);

        return response()->json(TriangulationSimulationResource::toArray($discounts, $deviceId), Response::HTTP_OK);
    }

    public function devicesAvailable(Request $request): JsonResponse
    {
        $devices = $this->deviceDiscountRepository
            ->devicesAvailable($request->user())
            ->pluck('device')
            ->unique()
            ->values();

        return response()->json($devices, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        hasPermissionOrAbort(TriangulationPermission::getFullName(TriangulationPermission::DELETE));
        $delete = $this->writeService->delete($id, auth()->user());

        if ($delete) {
            $message =  trans('discount::messages.delete_success');
            $status  = Response::HTTP_OK;
        } else {
            $message = trans('discount::messages.delete_failed');
            $status  = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return response()->json(['message' => $message], $status);
    }

    public function getBrandsOutsourced(Request $request)
    {
        $devicesOutsourced = $this->deviceService->getDevicesOutsourcedByNetwork($request->user());

        $brandsOutsourced = array_values($devicesOutsourced->groupBy('brand')->map(function ($device, $key) {
            return collect([
                    'id'    => $key,
                    'label' => ucwords(Str::lower($key))
            ]);
        })->toArray());

        return response()->json(collect([
            'brands'=> $brandsOutsourced,
            'models'=> $devicesOutsourced->toArray()
        ]), Response::HTTP_OK);
    }

    public function discountsOrRebateInSale(DiscountFormRequest $request)
    {
        $user    = $request->user();
        $filters = $request->validated();

        $filters['operation'] =  $request->get('operations');

        $discountRebate = $this->discountService->getDiscountOrRebate($user, $filters);

        return response()->json($discountRebate, Response::HTTP_OK);
    }
}
