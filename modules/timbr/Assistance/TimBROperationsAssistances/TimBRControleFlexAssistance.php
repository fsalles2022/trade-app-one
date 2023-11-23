<?php

declare(strict_types=1);

namespace TimBR\Assistance\TimBROperationsAssistances;

use Illuminate\Http\Response;
use TimBR\Adapters\TimBROrderControleFlexPayload;
use TimBR\Adapters\TimOrderResponseAdapter;
use TimBR\Services\TimBRService;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Logging\LogEnumerators;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;

class TimBRControleFlexAssistance implements TimBROperationsAssistanceInterface
{
    /** @var TimBRService */
    private $timBRService;

    /** @var SaleRepository */
    private $saleRepository;

    public function __construct(TimBRService $timBRService, SaleRepository $saleRepository)
    {
        $this->timBRService    = $timBRService;
        $this->saleRepository  = $saleRepository;
    }

    public function activate(Service $service, array $payload = []): ResponseAdapterAbstract
    {
        if ($service->status === ServiceStatus::REJECTED) {
            throw new ServiceAlreadyInProgress($service->status);
        }

        $payloadAdapted = TimBROrderControleFlexPayload::adapt($service);

        $timResponse = $this->callTim($service, $payloadAdapted);

        $this->saleRepository->pushLogService($service, $timResponse->toArray());

        $order = $timResponse->toArray()['order'] ?? null;

        if ($order !== null || $timResponse->getStatus() === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->saleRepository->updateService($service, [
                'status' => ServiceStatus::ACCEPTED
            ]);

            $response = new TimOrderResponseAdapter($timResponse);
            $response->pushAttributes(['message' => trans('timBR::messages.flex.success')]);

            return $response;
        }

        $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);

        integrationLogger(LogEnumerators::TIM_INTEGRATION_FAILED)
            ->tags(LogEnumerators::TIM_INTEGRATION_FAILED_TAGS)
            ->transaction($service->serviceTransaction)
            ->request($payloadAdapted)
            ->response($timResponse->toArray());

        return new TimOrderResponseAdapter($timResponse);
    }

    /** @param mixed[] $payload */
    protected function callTim(Service $service, array $payload): Responseable
    {
        $network  = $service->sale->pointOfSale['network']['slug'] ?? '';
        $cpf      = $service->sale->user['cpf'] ?? '';
        $protocol = data_get($service, 'operatorIdentifiers.protocol', '');

        return $this->timBRService->generateOrder($protocol, $network, $cpf, $payload);
    }
}
