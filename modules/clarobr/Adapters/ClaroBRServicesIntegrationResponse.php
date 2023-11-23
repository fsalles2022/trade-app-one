<?php
declare(strict_types=1);

namespace ClaroBR\Adapters;

use ClaroBR\Exceptions\ClaroExceptions;
use Illuminate\Http\JsonResponse;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\ServicesIntegrationResponseAbstract;
use TradeAppOne\Exceptions\RemotePaymentException;

class ClaroBRServicesIntegrationResponse extends ServicesIntegrationResponseAbstract
{
    public function settle(): JsonResponse
    {
        $operationMethod = strtolower($this->service->operation);

        return method_exists($this, $operationMethod)
            ? $this->$operationMethod()
            : $this->response;
    }

    /**
     * Invocado dinamicamente, não remover.
     */
    private function controle_facil(): JsonResponse
    {
        if ($this->service->remoteSale !== true) {
            return $this->response;
        }

        $dataContent = $this->response->getData();
        $paymentUrl  = $this->createUrl($dataContent);

        resolve(SaleRepository::class)->updateService($this->service, [
            'integratorPaymentURL' => $this->getIntegratorUrl($dataContent),
            'paymentUrl' => $paymentUrl
        ]);

        $dataContent->remoteSaleUrl = $paymentUrl;

        return $this->response->setData($dataContent);
    }

    private function getIntegratorUrl(\stdClass  $dataContent): string
    {
        $integratorUrl = ! empty($dataContent->data->link) ? json_decode($dataContent->data->link) : false;
        throw_if(empty($integratorUrl->url), ClaroExceptions::paymentUrlNotFound());

        return $integratorUrl->url;
    }

    private function createUrl(\stdClass  $dataContent): string
    {
        throw_if(
            (empty($dataContent->urlOrigin) || empty($this->service->serviceTransaction)),
            RemotePaymentException::paymentUrlNotCreated()
        );

        return $dataContent->urlOrigin . '/pagamento/' . base64_encode($this->service->serviceTransaction);
    }
}
