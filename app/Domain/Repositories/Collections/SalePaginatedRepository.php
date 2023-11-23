<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Factories\MongoDbConnector;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Filters\SalesFilter;

class SalePaginatedRepository
{
    const COLLECTION_NAME          = 'sales';
    public const QUANTITY_PER_PAGE = 10;

    private $mongoConnection;
    private $hierarchyRepository;


    public function __construct(MongoDbConnector $mongoConnection, HierarchyRepository $hierarchyRepository)
    {
        $this->mongoConnection     = $mongoConnection;
        $this->hierarchyRepository = $hierarchyRepository;
    }

    public function searchByFilters($user, array $parameters, int $page = 1, int $perPage = SalePaginatedRepository::QUANTITY_PER_PAGE): LengthAwarePaginator
    {
        $contextFilter = $this->getContextFilter($user);

        $queryFilters = (new SalesFilter($contextFilter))->build($parameters);

        return $this->paginate($queryFilters, $page, $perPage);
    }

    private function getContextFilter(User $user): array
    {
        if ($user->hasPermission(SalePermission::getFullName(SalePermission::CONTEXT_ALL))) {
            return [];
        }

        if ($user->hasPermission(SalePermission::getFullName(SalePermission::CONTEXT_NETWORK))) {
            return ['pointOfSale.network.slug' => $user->getNetwork()->slug];
        }

        $userSeeSalesBasedOnHierarchy = $user->hasPermission(SalePermission::getFullName(SalePermission::CONTEXT_HIERARCHY));

        $pointsOfSaleCollection = $this
            ->hierarchyRepository
            ->getPointsOfSaleThatBelongsToUser($user);

        $cnpjs = $pointsOfSaleCollection
            ->pluck('cnpj')
            ->toArray();

        if ($userSeeSalesBasedOnHierarchy) {
            return ['pointOfSale.cnpj' => ['$in' => $cnpjs]];
        }

        return ['user.cpf' => $user->cpf, 'pointOfSale.cnpj' => ['$in' => $cnpjs]];
    }

    private function paginate(array $filters, $currentPage = 1, $customPerPage = self::QUANTITY_PER_PAGE): LengthAwarePaginator
    {
        $currentPage = abs($currentPage);
        $sales       = $this->mongoConnection->getCollection(self::COLLECTION_NAME);

        $skips = $customPerPage * ($currentPage - 1);
        $total = $sales->count($filters);

        $options = [
            'skip'  => $skips,
            'limit' => $customPerPage,
            'sort'  => ['createdAt' => -1]
        ];

        $results  = $sales->find($filters, $options);
        $services = $this->getResultAsCollectionOfServices($results);

        return new LengthAwarePaginator(
            $this->castingValues($services),
            $total,
            $customPerPage,
            $currentPage
        );
    }

    private function getResultAsCollectionOfServices($collection): Collection
    {
        $servicesAlreadyIterated = iterator_to_array($collection);
        $instanceCollection      = new Collection();
        foreach ($servicesAlreadyIterated as $serviceArray) {
            $iteratorPureArray         = iterator_to_array($serviceArray);
            $saleInstance              = new Sale();
            $saleInstance->user        = data_get($iteratorPureArray, 'user');
            $saleInstance->pointOfSale = data_get($iteratorPureArray, 'pointOfSale');

            $instanceCollection->push(self::map($iteratorPureArray));
        }

        return $instanceCollection;
    }

    private static function map($servicePureArray)
    {
        $saleInstance = new Sale();
        $saleInstance->forceFill($servicePureArray);
        return $saleInstance;
    }

    public function searchByFiltersToIntegrators($user, array $parameters, $currentPage): LengthAwarePaginator
    {
        $contextFilter = $this->getContextFilter($user);

        $queryFilters = (new SalesFilter($contextFilter))->build($parameters);

        return $this->queryIntegrators($queryFilters, $currentPage);
    }

    private function queryIntegrators(array $filters, $currentPage): LengthAwarePaginator
    {
        $sales = $this->mongoConnection->getCollection(self::COLLECTION_NAME);

        $skips = self::QUANTITY_PER_PAGE * ($currentPage - 1);

        $options = [
            'skip'  => $skips,
            'limit' => self::QUANTITY_PER_PAGE,
            'sort'  => ['createdAt' => -1]
        ];

        $results = $sales->find($filters, $options);

        $services = $this->getResultAsCollectionOfServices($results);

        return new LengthAwarePaginator($services, 1, self::QUANTITY_PER_PAGE, 1);
    }

    public function searchBuybackByFilters(
        array $tradeInOperations,
        array $parameters,
        int $page = 1
    ): LengthAwarePaginator {
        $buybackFilter = ['services.operation' => ['$in' => $tradeInOperations]];

        $queryFilters = (new SalesFilter($buybackFilter))->build($parameters);

        return $this->paginate($queryFilters, $page);
    }

    /**
     * @param Collection $services
     * @return Collection
     */
    private function castingValues(Collection $services): Collection
    {
        return $services->map(static function (Sale $sale) {
            $storage = [];

            if ($services = $sale->services) {
                foreach ($services as $service) {
                    $price   = (float) $service->offsetGet('price');
                    $product = ! is_object($service->offsetGet('product')) ? (string) $service->offsetGet('product') : $service->offsetGet('product');

                    $service->offsetSet('price', $price);
                    $service->offsetSet('product', $product);

                    $storage[] = $service;
                }
            }

            $sale->services = $storage;
            $sale->total    = (float) ($sale->total ?? 0);

            return $sale;
        });
    }
}
