<?php

namespace TimBR\Assistance\TimBROperationsAssistances;

use ErrorException;
use Exception;
use Illuminate\Http\Response;
use TimBR\Adapters\M4u\TimBRM4uRequestAdapter;
use TimBR\Adapters\M4u\TimBRM4uResponseAdapter;
use TimBR\Adapters\TimBROrderExpressRequestAdapter;
use TimBR\Adapters\TimOrderResponseAdapter;
use TimBR\Connection\Headers\TimHeadersFactory;
use TimBR\Connection\TimExpress\TimBRExpressConnection;
use TimBR\Services\TimBRService;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Logging\LogEnumerators;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;

class TimBRControleExpressAssistance implements TimBROperationsAssistanceInterface
{
    public const MESSAGE_SALE_EXISTS = 'Já existe uma ordem com esse número';
    public const MESSAGE_SALE_TRY    = ' Numero de tentativa de adesao excedida para um unico msisdn';

    /** @var SaleRepository */
    protected $saleRepository;

    /** @var TimBRService */
    protected $timBRService;

    /** @var TimBRExpressConnection */
    protected $timExpress;

    public function __construct(
        SaleRepository $saleRepository,
        TimBRService $timBRService,
        TimBRExpressConnection $timExpress
    ) {
        $this->saleRepository  = $saleRepository;
        $this->timBRService    = $timBRService;
        $this->timExpress      = $timExpress;
    }

    public function activate(Service $service, array $payload = []): ResponseAdapterAbstract
    {
        $network      = data_get($service->sale->pointOfSale, 'network.slug');
        $userCpf      = data_get($service->sale->user, 'cpf');
        $userSergeant = TimHeadersFactory::make($network)->getSergeant($userCpf);

        if ($service->status === ServiceStatus::REJECTED) {
            throw new ServiceAlreadyInProgress($service->status);
        }

        if ($service->status === ServiceStatus::PENDING_SUBMISSION) {
            $m4uResponse = $this->callM4u($service, $userSergeant);

            $this->saleRepository->pushLogService($service, $m4uResponse->getOriginal());
            $toUpdate = [];

            if ($m4uResponse->isSuccess()) {
                $toUpdate['status'] = ServiceStatus::SUBMITTED;
                $this->saleRepository->updateService($service, $toUpdate);
            } else {
                $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
                return $m4uResponse;
            }
        }

        return $this->callForOrder($service);
    }

    public function callM4u(Service $service, string $userSergeant): ResponseAdapterAbstract
    {
        $adapted = TimBRM4uRequestAdapter::adapt($service, $userSergeant);

        $response      = $this->timExpress->customerSubscription($adapted, $service->eligibilityToken);
        $arrayResponse = $response->toArray();

        $description = data_get($arrayResponse, 'responseDescription');

        if (str_contains($description, self::MESSAGE_SALE_EXISTS)
            || str_contains($description, self::MESSAGE_SALE_TRY)) {
            $this->timExpress->cancelSubscription($service->eligibilityToken);
            $response = $this->timExpress->customerSubscription($adapted, $service->eligibilityToken);
            $this->timExpress->customerSubscription($adapted, $service->eligibilityToken);
        }

        return new TimBRM4uResponseAdapter($response);
    }

    protected function callForOrder(Service $service): ResponseAdapterAbstract
    {
        $adapted          = TimBROrderExpressRequestAdapter::adapt($service);
        $timResponse      = $this->callTim($service, $adapted);
        $timArrayResponse = $timResponse->toArray();

        $this->saleRepository->pushLogService($service, $timArrayResponse);

        try {
            if ($timResponse->getStatus() === Response::HTTP_INTERNAL_SERVER_ERROR || $identifiers = $timArrayResponse['order']) {
                $msisdn = data_get($identifiers, 'contract.msisdn', $service->msisdn);

                $this->saleRepository->updateService($service, [
                    'msisdn' => MsisdnHelper::addCountryCode(CountryAbbreviation::BR, $msisdn), //Necessary to not lost msisdn data in service
                    'status' => ServiceStatus::ACCEPTED,
                ]);
            } else {
                integrationLogger(LogEnumerators::TIM_INTEGRATION_FAILED)
                    ->tags(LogEnumerators::TIM_INTEGRATION_FAILED_TAGS)
                    ->transaction($service->serviceTransaction)
                    ->request($adapted)
                    ->response($timArrayResponse);
            }
        } catch (Exception $exception) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
        }

        $adapted = new TimOrderResponseAdapter($timResponse);

        if ($timResponse->getStatus() === Response::HTTP_OK) {
            $adapted = $this->selectMessage($adapted);
        }

        return $adapted;
    }

    public function callTim(Service $service, $adapted): Responseable
    {
        $network  = $service->sale->pointOfSale['network']['slug'] ?? '';
        $cpf      = $service->sale->user['cpf'] ?? '';
        $protocol = data_get($service, 'operatorIdentifiers.protocol', '');
        data_set($adapted, 'order.isSimulation', false);
        data_set($adapted, 'order.customer.isIlliterate', false);
        data_set($adapted, 'order.customer.disabilities', []);
        data_set($adapted, 'witness', []);

        return $this->timBRService->generateOrder($protocol, $network, $cpf, $adapted);
    }

    private function selectMessage(ResponseAdapterAbstract $adapted)
    {
        try {
            $message = trans('timBR::messages.express.acceptance');

            if (! str_contains($message, 'timBR::message')) {
                $adapted->pushAttributes(['message' => $message]);
            }
            return $adapted;
        } catch (ErrorException $exception) {
            return $adapted;
        }
    }
}
