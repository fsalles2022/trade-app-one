<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Importables\ValidateProviderToImport;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\UserDoesntBelongsToPointOfSaleException;

class PointOfSaleService extends BaseService
{
    /**
     * @var PointOfSaleRepository
     */
    private $repository;

    public function __construct(
        PointOfSaleRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function filter(array $parameters): LengthAwarePaginator
    {
        return $this->basefilter($parameters)->paginate(10);
    }

    public function filterWithoutPaginate(array $parameters): Collection
    {
        return $this->basefilter($parameters)
            ->select()
            ->get();
    }

    private function basefilter(array $parameters): Builder
    {
        return $this->repository
            ->filter($parameters, Auth::user())
            ->with('network', 'hierarchy')
            ->orderBy('pointsOfSale.slug', 'asc');
    }

    public function checkPermissionAndReturnPointOfSale(User $user, string $pointOfSaleId): PointOfSale
    {
        $pointsOfSaleAttachedToUser = $user->pointsOfSale;
        $pointOfSaleRequested       = $pointsOfSaleAttachedToUser->where('id', $pointOfSaleId)->first();

        if ($pointOfSaleRequested) {
            return $pointOfSaleRequested;
        }

        throw new UserDoesntBelongsToPointOfSaleException();
    }

    public function findOneBySlug($slug): PointOfSale
    {
        $pointOfSale = $this->repository->findOneBy('slug', $slug);

        if (! $pointOfSale instanceof PointOfSale) {
            throw new PointOfSaleNotFoundException();
        }

        return $pointOfSale;
    }

    public function findOneBySlugAndNetworkId(string $slug, int $networkId): ?PointOfSale
    {
        $pointOfSale = $this->repository->findOneWithFilters([
            'slug'      => $slug,
            'networkId' => $networkId
        ]);

        return $pointOfSale;
    }

    public function update($attributes, $cnpj): PointOfSale
    {
        $provider    = data_get($attributes, 'providerIdentifiers');
        $pointOfSale = $this->findOneByCnpj($cnpj);
        $hierarchy   = $this->hierarchyService->findOneHierarchyBySlug($attributes['hierarchy']['slug']);
        $network     = $this->networkService->find($pointOfSale->networkId);

        $line =  $this->adapterProvider($provider);

        $attributes['hierarchyId']         = $hierarchy->id;
        $attributes['providerIdentifiers'] = (new ValidateProviderToImport($network, $line, $pointOfSale))->make();

        $pointOfSale->update($attributes);

        return $pointOfSale;
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function delete(PointOfSale $pointOfSale)
    {
        return $this->repository->delete($pointOfSale);
    }

    public function findOneByCnpj(string $cnpj): PointOfSale
    {
        $pointOfSale = $this->repository->findOneBy('cnpj', $cnpj);
        if (! $pointOfSale instanceof PointOfSale) {
            throw new PointOfSaleNotFoundException();
        }

        return $pointOfSale;
    }

    public function findOneByProviderIdentifiers(string $key, string $code)
    {
        $pointOfSale = $this->repository->findOneByProviderIdentifiers($key, $code);

        if (! $pointOfSale instanceof PointOfSale) {
            throw new PointOfSaleNotFoundException();
        }

        return $pointOfSale;
    }

    public function getUserPointOfSaleLogged(User $user): Collection
    {
        return $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);
    }

    public function detachAllPointsOfSale(User $user): ?User
    {
        $user->pointsOfSale()->detach();
        return $user;
    }

    public function create(array $attributes): Model
    {
        $provider  = data_get($attributes, 'providerIdentifiers');
        $network   = $this->networkService->findOneBySlug($attributes['network']['slug']);
        $hierarchy = $this->hierarchyService->findOneHierarchyBySlug($attributes['hierarchy']['slug']);
        $line      = $this->adapterProvider($provider);

        $attributes['slug']                = str_slug($attributes['slug']);
        $attributes['networkId']           = $network->id;
        $attributes['hierarchyId']         = $hierarchy->id;
        $attributes['providerIdentifiers'] = (new ValidateProviderToImport($network, $line))->make();

        return $this->repository->create($attributes);
    }

    private function adapterProvider(array $providers): array
    {
        $line[ValidateProviderToImport::OI]         = data_get($providers, Operations::OI);
        $line[ValidateProviderToImport::TIM]        = data_get($providers, Operations::TIM);
        $line[ValidateProviderToImport::CLARO]      = data_get($providers, Operations::CLARO);
        $line[ValidateProviderToImport::NEXTEL_COD] = data_get($providers, 'NEXTEL.cod');
        $line[ValidateProviderToImport::NEXTEL_REF] = data_get($providers, 'NEXTEL.ref');

        return $line;
    }
}
