<?php

namespace Buyback\Services;

use Buyback\Repositories\OfferDeclinedRepository;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Exportables\OfferDeclinedExport;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\HierarchyService;

class OfferDeclinedService
{
    private $offerDeclinedRepository;
    private $hierarchyService;
    const ITEMS_PER_PAGE = 10;

    public function __construct(OfferDeclinedRepository $offerDeclinedRepository, HierarchyService $hierarchyService)
    {
        $this->offerDeclinedRepository = $offerDeclinedRepository;
        $this->hierarchyService        = $hierarchyService;
    }

    public function new(array $payloadFormatted)
    {
        return $this->offerDeclinedRepository->create($payloadFormatted);
    }

    public function getDeclinedOffersByUser(User $user): Builder
    {
        $pointsOfSale    = $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);
        $pointsOfSaleIds = $pointsOfSale->pluck('id')->toArray();
        return $this->offerDeclinedRepository->offersDeclinedByPointOfSaleIds($pointsOfSaleIds);
    }

    public function paginateDeclinedOffersByUser(User $user, array $parameters = [])
    {
        $declinedOffers = $this->getDeclinedOffersByUser($user);

        return $this->offerDeclinedRepository
            ->applyFilters($declinedOffers, $parameters)
            ->orderBy('createdAt', 'desc')
            ->paginate(self::ITEMS_PER_PAGE);
    }

    public function exportDeclinedOffersByUser(User $user)
    {
        $offersDeclined = $this->getDeclinedOffersByUser($user)->get();
        return (new OfferDeclinedExport($offersDeclined));
    }
}
