<?php

namespace TimBR\Assistance\TimBROperationsAssistances;

use Illuminate\Http\Response;
use Throwable;
use TimBR\Adapters\TimBROrderRequestAdapter;
use TimBR\Adapters\TimOrderResponseAdapter;
use TimBR\Services\TimBRService;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class TimBRPrePagoAssistance implements TimBROperationsAssistanceInterface
{
    /** @var SaleRepository  */
    protected $saleRepository;

    /** @var TimBRService */
    protected $timBRService;

    public function __construct(
        SaleRepository $saleRepository,
        TimBRService $timBRService
    ) {
        $this->saleRepository  = $saleRepository;
        $this->timBRService    = $timBRService;
    }

    /**
     * @throws Throwable
     */
    public function activate(Service $service, array $payload = []): ResponseAdapterAbstract
    {
        $adapted          = TimBROrderRequestAdapter::adapt($service);
        $timResponse      = $this->callTim($service, $adapted);
        $timArrayResponse = $timResponse->toArray();
        $this->saleRepository->pushLogService($service, $timArrayResponse);
        try {
            if ($timResponse->getStatus() === Response::HTTP_INTERNAL_SERVER_ERROR || $timArrayResponse['order']) {
                $this->saleRepository->updateService($service, [
                    'status' => ServiceStatus::APPROVED
                ]);
            }
        } catch (\Exception $exception) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
        }
        $adapted = new TimOrderResponseAdapter($timResponse);
        if ($timResponse->getStatus() === Response::HTTP_OK) {
            $adapted = $this->selectMessage($adapted, $service);
        }
        return $adapted;
    }

    /** @param mixed[] $payload */
    protected function callTim(Service $service, array $payload): Responseable
    {
        $network  = $service->sale->pointOfSale['network']['slug'] ?? '';
        $cpf      = $service->sale->user['cpf'] ?? '';
        $protocol = data_get($service, 'operatorIdentifiers.protocol', '');

        return $this->timBRService->generateOrder($protocol, $network, $cpf, $payload);
    }

    private function selectMessage(ResponseAdapterAbstract $adapted, Service $service)
    {
        try {
            $message = trans('timBR::messages.acceptance.prepago');

            if (! str_contains($message, 'timBR::messages.acceptance.')) {
                $adapted->pushAttributes(['message' => $message]);
            }
            return $adapted;
        } catch (\ErrorException $exception) {
            return $adapted;
        }
    }
}
