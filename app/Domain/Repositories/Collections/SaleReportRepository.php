<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\User;

class SaleReportRepository
{
    private $elasticConnection;
    private $hierarchyRepository;
    private $userRepository;

    public function __construct(
        ElasticConnection $elasticConnection,
        HierarchyRepository $hierarchyRepository,
        UserRepository $userRepository
    ) {
        $this->elasticConnection   = $elasticConnection;
        $this->hierarchyRepository = $hierarchyRepository;
        $this->userRepository      = $userRepository;
    }

    public function getFilteredByContext(ElasticQueryBuilder $elasticQueryBuilder): Collection
    {
        $queryBuilder = $this->filterByContext($elasticQueryBuilder);
        $collection   = $this->elasticConnection->execute($queryBuilder);

        return new Collection($collection);
    }

    public function getFilteredByContextUsingScroll(ElasticQueryBuilder $elasticQueryBuilder): Collection
    {
        $queryBuilder = $this->filterByContext($elasticQueryBuilder);
        $collection   = $this->elasticConnection->executeUsingScroll($queryBuilder);

        return new Collection($collection);
    }

    public function filterByContext(ElasticQueryBuilder $queryBuilder): ElasticQueryBuilder
    {
        /** @var User $user */
        $user = $this->userRepository->getAuthenticatedUser();

        $context = $user->getUserContext(SalePermission::NAME);

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

            return $queryBuilder->whereIn('pointofsale_network_slug', $networks)
                ->whereIn('service_operator', $operators);
        }

        switch ($context) {
            case ContextEnum::CONTEXT_ALL:
                return $queryBuilder;
            case ContextEnum::CONTEXT_NETWORK:
                $networksCollection = $this->hierarchyRepository->getNetworksThatBelongsToUser($user);
                $networks           = $networksCollection->pluck('slug')->toArray();

                return $queryBuilder->whereIn('pointofsale_network_slug', $networks);
            case ContextEnum::CONTEXT_HIERARCHY:
                $pointsOfSaleCollection = $this->hierarchyRepository->getPointsOfSaleThatBelongsToUser($user);
                $cnpjs                  = $pointsOfSaleCollection->pluck('cnpj')->toArray();

                return $queryBuilder->whereIn('pointofsale_cnpj', $cnpjs);
            case ContextEnum::CONTEXT_NON_EXISTENT:
            case ContextEnum::CONTEXT_OWN:
                return $queryBuilder->where('user_cpf', $user->cpf);
        }
    }

    public function executeWithoutContext(ElasticQueryBuilder $elasticQueryBuilder): Collection
    {
        $collection = $this->elasticConnection->execute($elasticQueryBuilder);
        return new Collection($collection);
    }
}
