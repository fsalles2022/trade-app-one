<?php


namespace Recommendation\Repositories;

use Recommendation\Models\Recommendation;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class RecommendationRepository extends BaseRepository
{
    protected $model = Recommendation::class;

    public function getByRegistrationAndPointOfSaleId(string $registration, int $pointOfSaleId = null)
    {
        return $this->createModel()->where([
            'pointOfSaleId' => $pointOfSaleId,
            'registration' => $registration
        ])->first();
    }

    public function getByRegistration(string $registration)
    {
        return $this->createModel()->where([
            'registration' => $registration
        ])->first();
    }
}
