<?php

namespace FastShop\Services;

use Carbon\Carbon;
use FastShop\Adapters\SimulationAdapter;
use FastShop\Connection\FastshopConnection;
use FastShop\Exceptions\FastshopExceptions;
use FastShop\Repositories\ProductRepository;
use Illuminate\Support\Collection;

class FastShopService
{

    private const DEFAULT_PRODUCT_TYPE = 'PRODUTO';
    private $fastshopConnection;
    private $productRepository;

    public function __construct(FastshopConnection $fastshopConnection, ProductRepository $productRepository)
    {
        $this->fastshopConnection = $fastshopConnection;
        $this->productRepository  = $productRepository;
    }

    public function getProducts(): Collection
    {
        $nextPage = true;
        $page     = 0;
        $results  = collect([]);

        while ($nextPage) {
            $filters = [
                'startdate' => '2019-05-01',
                'enddate' => '2019-05-31',
                'type' => self::DEFAULT_PRODUCT_TYPE,
                'page' => $page
            ];

            $response = $this->fastshopConnection->products($filters);

            $jsonSyntax = preg_replace("'}\s+{|}+\n{'", "},{", $response->get());
            $collection = collect(json_decode($jsonSyntax, true));

            $lastCollectionElement = $collection->last();
            $nextPage              = $lastCollectionElement['nextPage'] ? true : false;

            $results = $results->merge($collection);
            $page++;
        }
        return $results;
    }

    public function simulate(array $requestParams, array $requestQuery = []): Collection
    {
        $products = $this->productRepository->getByFilters($requestQuery);
        $response = $this->fastshopConnection->productPrice($requestParams);

        $devicePrice = $response->toArray();

        throw_if(
            empty($devicePrice) || $products->isEmpty(),
            FastshopExceptions::SimulateReturnEmptyDevicePrice()
        );

        return SimulationAdapter::adapter($products, $devicePrice);
    }
}
