<?php

namespace TradeAppOne\Domain\Services\Cancel;

use Carbon\Carbon;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Factories\CancelFactory;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;

class CancelServicesService
{
    private $saleRepository;
    private $saleService;

    public function __construct(SaleRepository $saleRepository, SaleService $saleService)
    {
        $this->saleRepository = $saleRepository;
        $this->saleService    = $saleService;
    }

    public function cancel(User $user, string $serviceTransaction): ?string
    {
        $service = $this->saleRepository->findInSale($serviceTransaction);
        throw_if($service === null, ServiceExceptions::notFound());
        $assistance = CancelFactory::make($service->operator);
        $message    = $assistance->cancel($user, $service);

        $this->addLogInService($service, $user, $message);
        return $message;
    }

    private function addLogInService(Service $service, User $user, string $message = ''): void
    {
        $this->saleService->pushLogService($service, [
            'name'    => "$user->firstName $user->lastName",
            'cpf'     => $user->cpf,
            'date'    => Carbon::now()->toIso8601String(),
            'action'  => 'CANCELLATION',
            'message' => $message,
            'success' => $service->status === ServiceStatus::CANCELED
        ]);
    }
}
