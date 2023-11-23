<?php

declare(strict_types=1);

namespace TimBR\Assistance\TimBROperationsAssistances;

use Illuminate\Http\Response;
use TimBR\Adapters\TimOrderResponseAdapter;
use TimBR\Enumerators\TimBRInvoiceTypes;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;

class TimBRBlackAssistance extends TimBRVarejoPremiumAssistance implements TimBROperationsAssistanceInterface
{
    public function activate(Service $service, array $payload = []): ResponseAdapterAbstract
    {
        $this->checkSaleTermStatus($service);

        if ($service->status === ServiceStatus::REJECTED) {
            throw new ServiceAlreadyInProgress($service->status);
        }

        if ($service->invoiceType === TimBRInvoiceTypes::CREDIT_CARD && $service->status === ServiceStatus::PENDING_SUBMISSION) {
            $m4uResponse = $this->callM4u($service);

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

        $timResponse = $this->callOrder($service);

        $adapted = new TimOrderResponseAdapter($timResponse);

        if ($timResponse->getStatus() === Response::HTTP_OK) {
            $this->sendWelcomeKitToCustomer($service);

            $adapted = $this->selectMessage($adapted, $service);
        }

        return $adapted;
    }
}
