<?php

namespace McAfee\Services;

use Carbon\Carbon;
use Gateway\Services\GatewayService;
use McAfee\Connection\McAfeeConnectionInterface;
use McAfee\Console\McAfeeTrialCommand;
use McAfee\Enumerators\McAfeeActions;
use McAfee\Exceptions\McAfeeExceptions;
use McAfee\Services\Queue\PaymentTransactionQueue;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class McAfeeService
{
    /** @var McAfeeConnectionInterface */
    private $mcAfeeConnection;

    /** @var GatewayService */
    private $gatewayService;

    /** @var SaleRepository */
    private $saleRepository;

    public function __construct(McAfeeConnectionInterface $connection, GatewayService $gateway, SaleRepository $saleRepository)
    {
        $this->mcAfeeConnection = $connection;
        $this->gatewayService   = $gateway;
        $this->saleRepository   = $saleRepository;
    }

    public function plans(User $user, string $operation = null)
    {
        return $this->mcAfeeConnection->plans($user, $operation);
    }

    public function newSubscription(Service $service, $trial = false): array
    {
        $mcAfeeResponse = $this->mcAfeeConnection->newSubscription($service);

        if ($mcAfeeResponse->isSuccess()) {
            $this->updateStatusThirdParty($service, ServiceStatus::APPROVED);
            return $mcAfeeResponse->getAdapted();
        }

        if ($trial === false) {
            $this->gatewayService->cancel($service);
        }

        throw McAfeeExceptions::mcAfeeErrorActivatingTheSale($mcAfeeResponse->getCode());
    }

    public function cancelSubscription(Service $service, User $user = null): array
    {
        $mcAfeeResponse = $this->mcAfeeConnection->cancelSubscription($service);

        if ($mcAfeeResponse->isSuccess()) {
            $this->updateStatusThirdParty($service, ServiceStatus::CANCELED);
            return $mcAfeeResponse->getAdapted();
        }

        $this->addLogInService($service, $user);

        throw McAfeeExceptions::mcAfeeErrorCancelingSubscription();
    }

    public function disconnectDevices(Service $service, User $user): array
    {
        $mcAfeeResponse = $this->mcAfeeConnection->disconnectDevices($service);
        if ($mcAfeeResponse->isSuccess()) {
            return $mcAfeeResponse->getAdapted();
        }
        $this->addLogInService($service, $user);
        throw McAfeeExceptions::mcAfeeErrorDisconnectingDevices();
    }

    private function addLogInService(Service $service, User $user = null, string $message = '')
    {
        $this->saleRepository->pushLogService($service, [
            'name' => $user ? "$user->firstName $user->lastName" : McAfeeTrialCommand::ACTION,
            'cpf' => $user ? $user->cpf : null,
            'date' => Carbon::now()->toIso8601String(),
            'action' => McAfeeActions::CANCELLATION,
            'message' => $message,
            'success' => $service->status === ServiceStatus::CANCELED
        ]);
    }

    private function updateStatusThirdParty(Service $service, string $status): Service
    {
        return $this->saleRepository->updateService($service, ['statusThirdParty' => $status]);
    }

    /** @var string[] $payment */
    public function updateStatusPayment(array $payment, string $serviceTransaction): void
    {
        PaymentTransactionQueue::dispatch($payment, $serviceTransaction);
    }
}
