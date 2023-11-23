<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Services\DeviceService;
use TradeAppOne\Http\Requests\DeviceFormRequest;

class DeviceController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function getDevices(): Collection
    {
        return $this->deviceService->getDevices();
    }

    public function getDevicesOutsourced(Request $request): JsonResponse
    {
        $devicesOutsourced = $this->deviceService->getDevicesOutsourcedByNetwork($request->user());

        return response()->json($devicesOutsourced, Response::HTTP_OK);
    }

    public function getDevicesPaginated(DeviceFormRequest $request): JsonResponse
    {
        $devices = $this->deviceService->devicesPaginated($request->validated());

        return response()->json($devices, Response::HTTP_OK);
    }

    public function getTypes(): JsonResponse
    {
        return response()->json($this->deviceService->getTypes(), Response::HTTP_OK);
    }

    public function getDeviceFilteredByType(DeviceFormRequest $request): JsonResponse
    {
        $devices = $this->deviceService->getDevicesFilteredByType($request->validated());
        return response()->json($devices, Response::HTTP_OK);
    }
}
