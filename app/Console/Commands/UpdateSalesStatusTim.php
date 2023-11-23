<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;

class UpdateSalesStatusTim extends Command
{
    protected $signature   = 'tim:update-sale-status 
    {status : The new status of the sale, the choices are: PENDING_SUBMISSION, SUBMITTED, ACCEPTED, APPROVED, CANCELED and REJECTED}
    {protocols* : The list of sales protocol}';
    protected $description = 'Updates the status of one or a set of sales.';
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        parent::__construct();
        $this->saleRepository = $saleRepository;
    }

    public function handle()
    {
        $availableStates = [
            ServiceStatus::ACCEPTED,
            ServiceStatus::APPROVED,
            ServiceStatus::CANCELED,
            ServiceStatus::PENDING_SUBMISSION,
            ServiceStatus::REJECTED,
            ServiceStatus::SUBMITTED
        ];
        $status          = $this->argument('status');
        $protocols       = $this->argument('protocols');

        throw_if((! in_array($status, $availableStates)), new InvalidServiceStatus());

        $salesUpdated   = 0;
        $salesWithError = [];
        foreach ($protocols as $protocol) {
            try {
                $sale = $this->saleRepository->findByProtocol($protocol);
                throw_if((! $sale), new SaleNotFoundException());
                $service = $this->getServices($sale, $protocol);
                $this->saleRepository->updateService($service, ['status' => $status]);
                $salesUpdated++;
            } catch (SaleNotFoundException $saleNotFoundException) {
                array_push($salesWithError, $protocol);
            }
        }

        $this->info("${salesUpdated} updated");

        $this->info(count($salesWithError)." with error");
        foreach ($salesWithError as $saleWithError) {
            $this->warn("${saleWithError}");
        }
    }

    private function getServices($sale, $serviceProtocol)
    {
        $services = $sale->services()->get();
        return $this->getOnlyOneService($serviceProtocol, $services);
    }

    private function getOnlyOneService(string $serviceProtocol, $services)
    {
        foreach ($services as $service) {
            $protocol = data_get($service, 'operatorIdentifiers.protocol');
            if ($protocol == $serviceProtocol) {
                return $service;
                break;
            }
        }
    }
}
