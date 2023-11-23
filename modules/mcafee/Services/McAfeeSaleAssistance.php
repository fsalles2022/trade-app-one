<?php

namespace McAfee\Services;

use Gateway\Services\GatewayService;
use Illuminate\Support\Facades\URL;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Exceptions\BuildExceptions;

class McAfeeSaleAssistance implements AssistanceBehavior
{
    public const NUMBER_PAYMENTS = 12;

    protected $mcAfeeService;
    protected $saleRepository;
    protected $gatewayService;
    protected $mcAfeeTrialService;

    public function __construct(McAfeeService $mcafee, SaleRepository $saleRepository, GatewayService $gateway, McAfeeTrialService $mcAfeeTrialService)
    {
        $this->mcAfeeService      = $mcafee;
        $this->saleRepository     = $saleRepository;
        $this->gatewayService     = $gateway;
        $this->mcAfeeTrialService = $mcAfeeTrialService;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $creditCard = data_get($payload, 'creditCard', null);

        if (data_get($service, 'remoteSale', false) === true &&
            empty($creditCard)
        ) {
            return response()->json($payload);
        }

        $creditCard['softDescriptor'] = Operations::MCAFEE;

        if ($this->isTrial($service)) {
            $this->trialSale($service, $creditCard);

            return ['message' => trans('mcAfee::messages.subscription.trial_success', ['label' => $service->label])];
        }

        $this->defaultSale($service, $creditCard);

        return ['message' => trans('mcAfee::messages.subscription.success', ['label' => $service->label])];
    }

    public function trialSale(Service $service, array $creditCard): void
    {
        $this->mcAfeeTrialService->authorize($service, $creditCard);

        $mcAfeeAdapter = $this->mcAfeeService->newSubscription($service, true);

        $this->saveStatements($service, $mcAfeeAdapter);
        $this->mcAfeeTrialService->schedule($service, $creditCard);
    }

    /**
     * @throws BuildExceptions
     */
    public function defaultSale(Service $service, array $creditCard): void
    {
        $serviceTokenized = $this->gatewayService->tokenize($service, $creditCard);
        $this->gatewayService->sale(
            $serviceTokenized,
            data_get($creditCard, 'times'),
            false,
            ['urlReturn' => secure_url(URL::route('urlReturn', ['serviceTransaction' => $service->serviceTransaction], false))]
        );

        $mcAfeeAdapter = $this->mcAfeeService->newSubscription($service);

        $this->saveStatements($service, $mcAfeeAdapter);
    }

    private function saveStatements(Service $service, array $mcAfeeAdapter): void
    {
        $license = $service->license ?? [];

        $license['mcAfeeReference']      = data_get($mcAfeeAdapter, '@attributes.REF');
        $license['mcAfeeActivationCode'] = data_get($mcAfeeAdapter, 'ITEMS.ITEM.PHONE.@attributes.ACTIVATIONCODE');
        $license['mcAfeeProductKey']     = data_get($mcAfeeAdapter, 'ITEMS.ITEM.PRODUCTKEY');

        $this->saleRepository->updateService($service, [
            'license' => $license,
            'status'  => ServiceStatus::APPROVED
        ]);
    }

    public function isTrial(Service $service): bool
    {
        $trial = data_get($service, 'license.trial');
        return ($trial === true || $trial === 1);
    }
}
