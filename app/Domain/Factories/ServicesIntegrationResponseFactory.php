<?php
declare(strict_types=1);

namespace TradeAppOne\Domain\Factories;

use ClaroBR\Adapters\ClaroBRServicesIntegrationResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use McAfee\Adapters\Response\McAfeeServicesIntegrationResponse;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Enumerators\Operations;

class ServicesIntegrationResponseFactory
{
    private const OPERATORS = [
        Operations::CLARO => ClaroBRServicesIntegrationResponse::class,
        Operations::MCAFEE => McAfeeServicesIntegrationResponse::class
    ];

    public static function make(Service $service, $response)
    {
        $creator = Arr::get(self::OPERATORS, $service->operator);

        if ($creator && ($response instanceof JsonResponse)) {
            if ($response->getStatusCode() === Response::HTTP_OK) {
                return (new $creator($service, $response))->settle();
            }
        }

        return $response;
    }
}
