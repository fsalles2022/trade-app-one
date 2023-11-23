<?php

namespace McAfee\Services;

use Gateway\Services\GatewayService;
use Illuminate\Support\Arr;
use McAfee\Enumerators\McAfeeStatus;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class McAfeeTrialService
{
    protected $gatewayService;
    protected $saleRepository;

    public function __construct(GatewayService $gatewayService, SaleRepository $saleRepository)
    {
        $this->gatewayService = $gatewayService;
        $this->saleRepository = $saleRepository;
    }

    public function authorize(Service $service, array $card): Service
    {
        $service = $this->gatewayService->tokenize($service, $card);
        $this->gatewayService->authorize($service);

        return $service;
    }

    public function schedule(Service $service, array $creditCard): Service
    {
        $license = $service->license ?? [];

        $license['trial'] = [
            'expiration' => MongoDateHelper::dateTimeToUtc(now()->addDays(30)),
            'status' => McAfeeStatus::ONGOING
        ];

        $this->saleRepository->updateService($service, [
            'license' => $license,
            'payment' => [
                'times' => Arr::get($creditCard, 'times')
            ]
        ]);

        return $service;
    }
}
