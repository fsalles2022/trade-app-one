<?php

declare(strict_types=1);

namespace McAfee\Adapters\Response;

use Illuminate\Http\JsonResponse;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\ServicesIntegrationResponseAbstract;
use TradeAppOne\Exceptions\RemotePaymentException;

class McAfeeServicesIntegrationResponse extends ServicesIntegrationResponseAbstract
{

    public function settle()
    {
        $operationMethod = strtolower($this->service->operation);

        return method_exists($this, $operationMethod)
            ? $this->$operationMethod()
            : $this->response;
    }

    /**
     * @throws \Throwable
     */
    private function mcafee_multi_access(): JsonResponse
    {
        return $this->makeRemoteSale();
    }

    /**
     * @throws \Throwable
     */
    private function mcafee_multi_access_trial(): JsonResponse
    {
        return $this->makeRemoteSale();
    }

    /**
     * @throws \Throwable
     */
    private function makeRemoteSale(): JsonResponse
    {
        if ($this->service->remoteSale !== true) {
            return $this->response;
        }

        $dataContent = $this->response->getData(true);
        $paymentUrl  = $this->createUrl($dataContent);

        resolve(SaleRepository::class)->updateService($this->service, [
            'paymentUrl' => $paymentUrl
        ]);

        $dataContent['remoteSaleUrl'] = $paymentUrl;

        return $this->response->setData($dataContent);
    }

    /**
     * @throws \Throwable
     */
    private function createUrl(array $dataContent): string
    {
        throw_if(
            (empty(data_get($dataContent, 'urlOrigin')) || empty($this->service->serviceTransaction)),
            RemotePaymentException::paymentUrlNotCreated()
        );

        return data_get($dataContent, 'urlOrigin') . '/pagamento-remoto/' . base64_encode($this->service->serviceTransaction);
    }
}
