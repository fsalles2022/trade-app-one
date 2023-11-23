<?php

namespace McAfee\Console;

use Carbon\Carbon;
use Gateway\Services\GatewayService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use McAfee\Enumerators\McAfeeStatus;
use McAfee\Services\McAfeeSaleAssistance;
use McAfee\Services\McAfeeService;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class McAfeeTrialCommand extends Command
{
    public const ACTION                      = 'MCAFEE_TRIAL_COMMAND';
    public const ERROR_PAYMENT_AND_CANCELING = 'MCAFEE_ERROR_PAYMENT_AND_CANCELING';
    public const ERROR_PAYMENT               = 'MCAFEE_ERROR_PAYMENT';

    protected $signature   = 'mcAfee:trial';
    protected $description = 'Billing McAfee Trial';

    protected $mcAfeeService;
    protected $saleRepository;
    protected $gatewayService;

    public function handle(McAfeeService $mcAfeeService, SaleRepository $saleRepository, GatewayService $gatewayService): void
    {
        $this->mcAfeeService  = $mcAfeeService;
        $this->saleRepository = $saleRepository;
        $this->gatewayService = $gatewayService;

        $services = $this->findServices();

        $this->output->progressStart($services->count());

        foreach ($services as $service) {
            $this->output->progressAdvance();
            $this->bill($service);
        }

        $this->output->progressFinish();
    }

    public function findServices(): Collection
    {
        $sales = $this->filterCriteria(Sale::query())->get();

        $services = collect();

        foreach ($sales as $sale) {
            $services = $services->merge(
                $this->filterCriteria($sale->services, '')
            );
        }

        return $services;
    }

    public function filterCriteria($builder, $prefix = 'services.')
    {
        return $builder
            ->where($prefix.'status', '=', ServiceStatus::APPROVED)
            ->where($prefix.'operation', '=', Operations::MCAFEE_MULTI_ACCESS_TRIAL)
            ->where($prefix.'license.trial.status', '=', McAfeeStatus::ONGOING)
            ->where($prefix.'license.trial.expiration', '<=', MongoDateHelper::dateTimeToUtc(now()->endOfDay()));
    }

    private function bill(Service $service): void
    {
        try {
            $times = data_get($service, 'payment.times', McAfeeSaleAssistance::NUMBER_PAYMENTS);
            $this->gatewayService->sale($service, $times);
            $this->updateServiceWithSuccess($service);
        } catch (\Exception $exception) {
            if (data_get($service, 'retryPayment')) {
                $this->cancel($service, $exception);
                return;
            }

            $this->updateServiceWithError($service, $exception, true);
        }
    }

    private function cancel(Service $service, $exception): void
    {
        try {
            $this->mcAfeeService->cancelSubscription($service);
            $this->updateServiceWithError($service, $exception);
        } catch (\Exception $exception) {
            $this->errorCancelSubscription($service, $exception);
        }
    }

    private function updateServiceWithError(Service $service, \Exception $exception, bool $retryPayment = false): void
    {
        $attributes = $this->adapterAttributesToUpdate($service, $retryPayment);

        $attributes['log']   = $service->log ?? [];
        $attributes['log'][] = [
            'action'  => self::ACTION,
            'message'    => trans('mcAfee::messages.error_billing_trial'),
            'info' => $exception->getMessage(),
            'status'  => self::ERROR_PAYMENT,
            'date'    => MongoDateHelper::dateTimeToUtc(now())
        ];

        $this->saleRepository->updateService(
            $service,
            $attributes
        );
    }

    /** @return mixed[] */
    private function adapterAttributesToUpdate(Service $service, bool $retryPayment = false): array
    {
        $license = $service->license ?? [];

        if ($retryPayment === true) {
            $license['trial']['status'] =  McAfeeStatus::ONGOING;

            $expirationDate = data_get($license, 'trial.expiration', MongoDateHelper::now());

            $license['trial']['expiration'] = MongoDateHelper::dateTimeToUtc(
                Carbon::parse(MongoDateHelper::millisecondsToFormat($expirationDate))->addDays(15)
            );

            return array_filter([
                'retryPayment'  => true,
                'license'       => $license,
                'status'        => ServiceStatus::APPROVED,
            ]);
        }

        $license['trial']['status'] = McAfeeStatus::REJECTED;

        return array_filter([
            'license' => $license,
            'status'  => ServiceStatus::CANCELED,
        ]);
    }

    private function errorCancelSubscription(Service $service, $exception): void
    {
        $logs = $service->log ?? [];

        $logs[] = [
            'action'  => self::ACTION,
            'message'    => trans('mcAfee::messages.trial_cancel_error'),
            'info' => $exception->getMessage(),
            'status'  => self::ERROR_PAYMENT_AND_CANCELING,
            'date'    => MongoDateHelper::dateTimeToUtc(now())
        ];

        $this->saleRepository->updateService($service, [
            'log' => $logs
        ]);

        Log::channel('sentryLogger')->critical(self::ACTION, [
            'serviceTransaction' => $service->serviceTransaction,
            'message' => $exception->getMessage(),
            'status' => self::ERROR_PAYMENT_AND_CANCELING,
            'date' => now()->toIso8601String()
        ]);
    }

    private function updateServiceWithSuccess(Service $service): void
    {
        $license                    = $service->license ?? [];
        $license['start']           = MongoDateHelper::dateTimeToUtc(now());
        $license['expiration']      = MongoDateHelper::dateTimeToUtc(now()->addDays(365));
        $license['trial']['status'] = McAfeeStatus::FINISHED;

        $this->saleRepository->updateService($service, [
            'license' => $license
        ]);
    }
}
