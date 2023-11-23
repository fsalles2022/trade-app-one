<?php

namespace Voucher\Http\Controllers;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Http\Controllers\Controller;
use Voucher\Http\Requests\CancelVoucherFormRequest;
use Voucher\Http\Requests\UserVoucherFormRequest;
use Voucher\Http\Requests\UseVoucherFormRequest;
use Voucher\Services\VoucherService;

class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public function useDiscount(UseVoucherFormRequest $request): Service
    {
        return $this->voucherService->useDiscount($request->validated());
    }

    public function checkVoucherIsAvailable(string $serviceTransaction): Collection
    {
        return $this->voucherService->checkVoucherIsAvailable($serviceTransaction);
    }

    public function availableDiscounts(string $cpf): ?Collection
    {
        return $this->voucherService->availableDiscounts($cpf);
    }

    public function cancelWithoutChargeback(string $transactionId, CancelVoucherFormRequest $request): ?Service
    {
        return $this->voucherService->cancel($transactionId, $request->validated(), false);
    }

    public function cancelWithChargeback(string $transactionId, CancelVoucherFormRequest $request): ?Service
    {
        return $this->voucherService->cancel($transactionId, $request->validated(), true);
    }

    public function getDiscountToChangeDevice(string $transactionId, string $newIMEI): array
    {
        return $this->voucherService->getNewDiscountDevice($transactionId, $newIMEI);
    }

    public function applyDiscountForDevice(string $transactionId, string $imei): array
    {
        return $this->voucherService->applyDiscountForDevice($transactionId, $imei);
    }
}
