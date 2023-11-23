<?php


namespace TradeAppOne\Domain\Services;

use Illuminate\Http\JsonResponse;
use TradeAppOne\Domain\Models\Collections\Service;

abstract class ServicesIntegrationResponseAbstract
{
    /**
     * @var Service
     */
    protected $service;
    /**
     * @var JsonResponse
     */
    protected $response;

    public function __construct(Service $service, JsonResponse $response)
    {
        $this->service  = $service;
        $this->response = $response;
    }

    abstract public function settle();
}
