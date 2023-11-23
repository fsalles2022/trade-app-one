<?php

declare(strict_types=1);

namespace Bulletin\Service;

use Bulletin\Enumerators\BulletinFilters;
use Bulletin\Models\Bulletin;
use Bulletin\Repositories\BulletinRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BulletinServices
{
    /** @var BulletinRepository */
    private $bulletinRepository;


    public function __construct(BulletinRepository $bulletinRepository)
    {
        $this->bulletinRepository = $bulletinRepository;
    }

    public function getBulletins(): Builder
    {
        return $this->bulletinRepository->getAll();
    }

    /**
     * @param Mixed[] $attributes
     * @return Bulletin[]
     */
    public function registerBulletins(array $attributes): array
    {
        return $this->bulletinRepository->save($attributes);
    }

    /**
     * @param Mixed[] $attributes
     * @param Bulletin $bulletin
     * @return bool
     */
    public function update(array $attributes, Bulletin $bulletin): bool
    {
        return $this->bulletinRepository->update($attributes, $bulletin);
    }

    /**
     * @param Mixed[] $attributes
     * @param Bulletin $bulletin
     * @return bool
     * @throws \Throwable
     */
    public function changeActivationStatus(array $attributes, Bulletin $bulletin): bool
    {
        return $this->bulletinRepository->updateStatus($attributes, $bulletin);
    }

    /**
     * @param Bulletin $bulletin
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Bulletin $bulletin): ?bool
    {
        return $this->bulletinRepository->delete($bulletin);
    }

    public function bulletinByUser(): Collection
    {
        return $this->bulletinRepository->getActiveUserBulletins();
    }

    /**
     * @return \Illuminate\Support\Collection|mixed
     */
    public function getFiltersByAuthUser()
    {
        $cpfUser = Auth::user()->cpf;

        if ($filters = Cache::get(BulletinFilters::CACHE . $cpfUser)) {
            return $filters;
        }

        $filters =  $this->bulletinRepository->getAuthorizedStructureByUserAuth();
        Cache::put(BulletinFilters::CACHE . $cpfUser, $filters, 60);

        return $filters;
    }

    /** @param Bulletin $bulletin */
    public function seen(Bulletin $bulletin): void
    {
        $this->bulletinRepository->updateBulletinsUsers($bulletin);
    }
}
