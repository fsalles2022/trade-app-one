<?php

declare(strict_types=1);

namespace TimBR\Assistance\TimBROperationsAssistances;

use Illuminate\Http\Response;
use TimBR\Adapters\M4u\TimBRM4uRequestAdapter;
use TimBR\Adapters\M4u\TimBRM4uResponseAdapter;
use TimBR\Adapters\TimBROrderRequestAdapter;
use TimBR\Enumerators\TimBrScanSaleTermStatus;
use TimBR\Exceptions\BrScanSaleTermStatusException;
use TimBR\Services\BrScanService;
use TimBR\Services\TimBRService;
use TradeAppOne\Domain\HttpClients\Responseable;
use TimBR\Adapters\TimOrderResponseAdapter;
use TimBR\Connection\Headers\TimHeadersFactory;
use TimBR\Connection\TimBRConnection;
use TimBR\Connection\TimExpress\TimBRExpressConnection;
use TimBR\Enumerators\M4USubscriptionMessages;
use TimBR\Enumerators\TimBRInvoiceTypes;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Logging\LogEnumerators;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;

abstract class TimBRVarejoPremiumAssistance implements TimBROperationsAssistanceInterface
{
    /** @var SaleRepository */
    protected $saleRepository;

    /** @var TimBRService */
    protected $timBRService;

    /** @var BrScanService */
    protected $brScanService;

    public function __construct(
        SaleRepository $saleRepository,
        TimBRService $timBRService,
        TimBRExpressConnection $timExpress,
        BrScanService $brScanService
    ) {
        $this->saleRepository  = $saleRepository;
        $this->timBRService    = $timBRService;
        $this->timExpress      = $timExpress;
        $this->brScanService   = $brScanService;
    }

    protected function callM4u(Service $service): ResponseAdapterAbstract
    {
        $adapted = TimBRM4uRequestAdapter::adapt(
            $service,
            $this->getSergeant($service)
        );

        $response      = $this->timExpress->customerSubscription($adapted, $service->eligibilityToken);
        $arrayResponse = $response->toArray();

        $description = data_get($arrayResponse, 'responseDescription');

        if (str_contains($description, M4USubscriptionMessages::MESSAGE_SALE_EXISTS)
            || str_contains($description, M4USubscriptionMessages::MESSAGE_SALE_TRY)) {
            $this->timExpress->cancelSubscription($service->eligibilityToken);
            $response = $this->timExpress->customerSubscription($adapted, $service->eligibilityToken);
            $this->timExpress->customerSubscription($adapted, $service->eligibilityToken);
        }

        return new TimBRM4uResponseAdapter($response);
    }

    protected function getSergeant(Service $service): string
    {
        $network = data_get($service->sale->pointOfSale, 'network.slug');
        $userCpf = data_get($service->sale->user, 'cpf');

        return TimHeadersFactory::make($network)->getSergeant($userCpf);
    }

    protected function callOrder(Service $service): Responseable
    {
        $adapted          = TimBROrderRequestAdapter::adapt($service);
        $timResponse      = $this->callTim($service, $adapted);
        $timArrayResponse = $timResponse->toArray();

        $this->saleRepository->pushLogService($service, $timArrayResponse);

        try {
            if ($timResponse->getStatus() === Response::HTTP_INTERNAL_SERVER_ERROR || $timArrayResponse['order']) {
                integrationLogger(LogEnumerators::TIM_INTEGRATION_SUCCESS)
                    ->tags(LogEnumerators::TIM_INTEGRATION_SUCCESS_TAGS)
                    ->transaction($service->serviceTransaction)
                    ->request($adapted)
                    ->response($timArrayResponse);

                $this->saleRepository->updateService($service, [
                    'status' => ServiceStatus::ACCEPTED,
                ]);
            } else {
                integrationLogger(LogEnumerators::TIM_INTEGRATION_FAILED)
                    ->tags(LogEnumerators::TIM_INTEGRATION_FAILED_TAGS)
                    ->transaction($service->serviceTransaction)
                    ->request($adapted)
                    ->response($timArrayResponse);
            }
        } catch (\Exception $exception) {
            integrationLogger(LogEnumerators::TIM_INTEGRATION_FAILED)
                ->tags(LogEnumerators::TIM_INTEGRATION_FAILED_TAGS)
                ->transaction($service->serviceTransaction)
                ->request($adapted)
                ->response($timArrayResponse);
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
        }

        return $timResponse;
    }

    /** @param mixed[] $payload */
    protected function callTim(Service $service, array $payload): Responseable
    {
        $network  = $service->sale->pointOfSale['network']['slug'] ?? '';
        $cpf      = $service->sale->user['cpf'] ?? '';
        $protocol = data_get($service, 'operatorIdentifiers.protocol', '');

        return $this->timBRService->generateOrder($protocol, $network, $cpf, $payload);
    }

    /** @throws \Throwable */
    protected function selectMessage(ResponseAdapterAbstract $adapted, Service $service): ResponseAdapterAbstract
    {
        try {
            $areaCode = $service->areaCode;
            $message  = trans('timBR::messages.acceptance.' . $areaCode);

            if (filled($areaCode) && ! str_contains($message, 'timBR::messages.acceptance.')) {
                $adapted->pushAttributes(['message' => $message]);
            }
            return $adapted;
        } catch (\Throwable $exception) {
            return $adapted;
        }
    }

    /** @throws BrScanSaleTermStatusException */
    protected function checkSaleTermStatus(Service $service): void
    {
        $saleTerm = $this->brScanService->getSaleTermStatus((int) data_get($service, 'authenticate.linkId'));

        if ($saleTerm['status'] !== TimBrScanSaleTermStatus::SUCCESS_STATUS) {
            throw new BrScanSaleTermStatusException($saleTerm['mensagem'] ?? '');
        }
    }

    protected function sendWelcomeKitToCustomer(Service $service): void
    {
        $this->brScanService->sendWelcomeKit((int) data_get($service, 'authenticate.linkId'));
    }
}
