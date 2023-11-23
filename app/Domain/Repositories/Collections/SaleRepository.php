<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Integrators\Enumerators\Integrators;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Filters\SaleRepositoryFilter;
use TradeAppOne\Domain\Repositories\Filters\SalesFilter;
use TradeAppOne\Domain\Repositories\Traits\SaleContext;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundByCustomer;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use Illuminate\Support\Collection as CollectionAlias;

class SaleRepository extends BaseRepository
{
    use SaleContext;

    protected $model = Sale::class;
    protected $hierarchyRepository;

    public function __construct(HierarchyRepository $hierarchyRepository)
    {
        $this->hierarchyRepository = $hierarchyRepository;
    }

    public function save(Sale $sale)
    {
        foreach ($sale->services as $service) {
            $service->validate();
        }
        $sale->save();
        return $sale;
    }

    public function find($saleTransaction)
    {
        return $this->createModel()->where('saleTransaction', $saleTransaction)->first();
    }

    public function paginateBuyback($filters, $perPage = 10)
    {
        $queryBuilder = $this->createModel()->newQuery();

        $query = $this->filter($queryBuilder, $filters);
        $query->where('services.sector', Operations::TRADE_IN);
        $query->orderBy('createdAt', 'desc');

        return $query->paginate($perPage);
    }

    private function filter(Builder $context, array $filters): Builder
    {
        return (new SaleRepositoryFilter($context))
            ->apply($filters)
            ->getQuery();
    }

    public function paginate($filters, $perPage = 10)
    {
        $queryWithContext = $this->filterByContext();

        $query = $this->filter($queryWithContext, $filters);
        $query->orderBy('createdAt', 'desc');

        return $query->paginate($perPage);
    }

    public function findOneBy($key, $value)
    {
        return $this->createModel()
            ->where($key, $value)
            ->orderBy('createdAt', 'desc')
            ->paginate(10);
    }

    public function makeService(array $attributes): Service
    {
        $service      = new Service();
        $service->_id = new ObjectId();

        $service->fill($attributes);

        return $service;
    }

    /**
     * @param User $user
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAllActivationByContext(
        User $user,
        array $parameters = [],
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        $contextFilter = $this->getContextFilter($user);
        $filters       = $this->mountFiltersByContext($contextFilter, $parameters);

        $query = $this->createModel()
            ->whereIn(
                'services.operator',
                array_keys(Operations::TELECOMMUNICATION_OPERATORS)
            )
            ->where($filters);

        if ($skip !== null && $take !== null) {
            return $query->skip($skip)->take($take)->get();
        }

        return $query->get();
    }

    /**
     * @param User $user
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAllSecuritySystemsByContext(
        User $user,
        array $parameters = [],
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        $contextFilter = $this->getContextFilter($user);
        $filters       = $this->mountFiltersByContext($contextFilter, $parameters);

        $query = $this->createModel()
            ->whereIn(
                'services.operator',
                array_keys(Operations::SECURITY_OPERATORS)
            )
            ->where($filters);

        if ($skip !== null && $take !== null) {
            return $query->skip($skip)->take($take)->get();
        }

        return $query->get();
    }

    /**
     * @param User $user
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAllBuybackByContext(
        User $user,
        array $parameters = [],
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        $contextFilter = $this->getContextFilter($user);
        $filters       = $this->mountFiltersByContext($contextFilter, $parameters);

        $query = $this->createModel()
            ->where('services.sector', Operations::TRADE_IN)
            ->where($filters);

        if ($skip !== null && $take !== null) {
            return $query->skip($skip)->take($take)->get();
        }

        return $query->get();
    }

    /**
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAll(
        array $parameters = [],
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        $filters = $this->mountFiltersByContext([], $parameters);

        $query = $this->createModel()->where($filters);

        if ($skip !== null && $take !== null) {
            return $query->skip($skip)->take($take)->get();
        }

        return $query->get();
    }

    /**
     * @param mixed[] $contextFilters
     * @param mixed[] $parameters
     * @return mixed[]
     */
    private function mountFiltersByContext(array $contextFilters, array $parameters): array
    {
        $salesFilters = new SalesFilter($contextFilters);

        return $salesFilters->build($this->transformFilters($parameters));
    }

    /**
     * @param mixed[] $filters
     * @return mixed[]
     */
    private function transformFilters(array $filters): array
    {
        if (key_exists('pointsOfSale', $filters) && count($filters['pointsOfSale']) > 0) {
            unset($filters['hierarchies']);
        }

        return $filters;
    }

    /** @return mixed[] */
    private function getContextFilter(User $user): array
    {
        if ($user->isPromoter()) {
            $networks = $user->channels()
                ->with('networks')
                ->get()
                ->pluck('networks')
                ->collapse()
                ->pluck('slug')
                ->toArray();

            $operators = $user
                ->operators
                ->pluck('slug')
                ->toArray();

            return [
                'pointOfSale.network.slug' => [
                    '$in' => $networks
                ],
                'services.operator' => [
                    '$in' => $operators
                ]
            ];
        }

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

    public function pushLogService(Service $instance, array $log = []): Service
    {
        $logs = $instance->log ?? [];
        array_push($logs, $log);
        $this->updateService($instance, ['log' => $logs]);
        return $instance;
    }

    /** @param string[] $paymentTransaction */
    public function updatePaymentStatus(Service $service, array $paymentTransaction): Service
    {
        $paymentTransactions = data_get($service, 'payment.log', []);
        array_push($paymentTransactions, $paymentTransaction);

        return $this->updateService(
            $service,
            [
                'payment.status' => data_get($paymentTransaction, 'status', ''),
                'payment.gatewayStatus' => data_get($paymentTransaction, 'status', ''),
                'payment.log' => $paymentTransactions
            ]
        );
    }

    /** @param mixed[] $attributes */
    public function updateService(Service $instance, array $attributes = []): Service
    {
        $instance->forceFill($attributes);
        $instance->touch();
        if ($instance->sale) {
            $instance->sale->touch();
        }
        $instance->save();

        return $instance;
    }

    public function updateSale(Sale $instance, array $attributes = []): Sale
    {
        $instance->forceFill($attributes);
        $instance->touch();
        $instance->save();

        return $instance;
    }

    public function findByProtocol(string $serviceProtocol): ?Sale
    {
        return $this->createModel()->where('services.operatorIdentifiers.protocol', $serviceProtocol)->first();
    }

    /**
     * @param string $cpf
     * @param string[]|null $status
     * @return Builder
     */
    public function findByCustomer(string $cpf, ?array $status = [ServiceStatus::APPROVED]): Builder
    {
        return $this->createModel()->where('services.customer.cpf', $cpf)->whereIn('services.status', $status);
    }

    public function findByImeiAndStatus(string $imei, array $status): ?Sale
    {
        return $this->createModel()
            ->where('services.imei', $imei)
            ->whereIn('services.status', $status)
            ->first();
    }

    public function findInSale(string $serviceTransaction): ?Service
    {
        $sale = $this->createModel()->where('services.serviceTransaction', $serviceTransaction)->first();

        if (! $sale instanceof Sale) {
            throw new SaleNotFoundException();
        }

        return $sale
            ? $sale->services()->where('serviceTransaction', $serviceTransaction)->first()
            : null;
    }

    public function findByIntegratorId(string $saleId, string $serviceId, string $integrator): ?Service
    {
        $service = null;
        switch ($integrator) {
            case Integrators::SUN:
                $sale    = $this->createModel()
                    ->where('services.operatorIdentifiers.idVenda', $saleId)
                    ->first();
                $service = $sale ? $sale->services()
                    ->where('operatorIdentifiers.idServico', $serviceId)
                    ->first() : null;
                break;
            case Integrators::SIV:
                $sale    = $this->createModel()
                    ->where('services.operatorIdentifiers.venda_id', (int) $saleId)
                    ->first();
                $service = $sale ? $sale->services()
                    ->where('operatorIdentifiers.servico_id', (int) $serviceId)
                    ->first() : null;
                break;
        }
        return $service;
    }

    public function getByFilters($filters)
    {
        $queryBuilder = $this->createModel()->newQuery();

        $query = $this->filter($queryBuilder, $filters);
        $query->orderBy('createdAt', 'desc');

        return $query->get();
    }

    public function getSalesByCustomerCpf(?string $customerCpf): CollectionAlias
    {
        /** @var Collection $sales */
        $sales = $this->createModel()
            ->where('services.customer.cpf', $customerCpf)
            ->with('services')
            ->get();

        throw_if($sales->isEmpty(), new SaleNotFoundByCustomer());

        return $sales;
    }

    public function updateImei(Service $service, string $imei): Service
    {
        return $this->updateService($service, ['imei' => $imei]);
    }
}
