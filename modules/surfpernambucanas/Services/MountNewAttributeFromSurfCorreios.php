<?php

declare(strict_types=1);

namespace SurfPernambucanas\Services;

use Closure;
use SurfPernambucanas\Exceptions\PagtelExceptions;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Domain\Enumerators\Operations;

class MountNewAttributeFromSurfCorreios implements MountNewAttributesService
{
    /** @var string[] */
    protected const OPERATIONS_SEARCH_PRODUCT_BY_ID = [
        Operations::SURF_CORREIOS_PRE,
        Operations::SURF_CORREIOS_SMART_CONTROL
    ];

    /** @var PagtelService */
    protected $pagtelService;

    public function __construct(PagtelService $pagtelService)
    {
        $this->pagtelService = $pagtelService;
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    public function getAttributes(array $service): array
    {
        $product = $this->getProductByService($service);

        $type  = data_get($product, 'type');
        $price = (float) data_get($product, 'price', 0);

        $productLabel = data_get($product, 'label');
        $label        = data_get($service, 'label', $productLabel);

        return compact('price', 'label', 'type');
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    protected function getProductByService(array $service): array
    {
        $operation = data_get($service, 'operation');

        $productIdentifier = data_get($service, 'product', '');

        if (in_array($operation, self::OPERATIONS_SEARCH_PRODUCT_BY_ID)) {
            return $this->searchActivationPlanById($productIdentifier);
        }

        $msisdn = data_get($service, 'msisdn', '');

        return $this->searchPlanByMsisdnAndName($msisdn, $productIdentifier);
    }

    /** @return mixed[] */
    protected function searchPlanByMsisdnAndName(string $msisdn, string $nameToSearch): array
    {
        return $this->searchProduct(
            $this->getPagtelPlansAdaptedByMsisdn($msisdn),
            function (array $plan) use ($nameToSearch): bool {
                return data_get($plan, 'label') === $nameToSearch;
            }
        );
    }

    /** @return mixed[] */
    protected function searchActivationPlanById(string $id): array
    {
        return $this->searchProduct(
            $this->getPagtelActivationPlansAdapted(),
            function (array $product) use ($id): bool {
                return data_get($product, 'id') === $id;
            }
        );
    }

    /** @return mixed[] */
    protected function getPagtelPlansAdaptedByMsisdn(string $msisdn): array
    {
        $response = $this->pagtelService->plans($msisdn)->getAdapted();

        return data_get($response, 'plans', []);
    }

    /** @return mixed[] */
    protected function getPagtelActivationPlansAdapted(): array
    {
        $response = $this->pagtelService->activationPlans()->getAdapted();

        return data_get($response, 'plans', []);
    }

    /**
     * @param array[] $products
     * @return mixed[]
     */
    protected function searchProduct(array $products, Closure $function): array
    {
        $products = collect($products);

        $planIndex = $products->search($function);

        throw_if($planIndex === false, PagtelExceptions::planNotFound());

        return $products->get($planIndex);
    }
}
