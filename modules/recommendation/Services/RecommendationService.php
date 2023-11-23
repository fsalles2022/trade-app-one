<?php


namespace Recommendation\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Recommendation\Adapters\IndicatedAdapter;
use Recommendation\Models\Recommendation;
use Recommendation\Repositories\RecommendationRepository;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Services\BaseService;

class RecommendationService extends BaseService
{
    private $repository;

    public function __construct(RecommendationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function indicated(array $requestValidated): array
    {
        $registration  = data_get($requestValidated, 'registration');
        $pointOfSaleId = data_get(Auth::user()->pointsOfSale->first(), 'id');
        $indicated     = $this->repository->getByRegistrationAndPointOfSaleId($registration, $pointOfSaleId);
        return IndicatedAdapter::adapter($indicated);
    }

    public function getRecommendationByRegistration(string $registration): ?Recommendation
    {
        return $this->repository->getByRegistration($registration);
    }

    public function createRecommendation(array $personData): Model
    {
        return $this->repository->create([
            'name' => data_get($personData, 'name'),
            'statusCode' => data_get($personData, 'statusCode'),
            'registration' => data_get($personData, 'registration'),
            'pointOfSaleId' => data_get($personData, 'pointOfSaleId')
        ]);
    }

    public function updateRecommendation(Recommendation $recommendation, array $personData): Model
    {
        return $this->repository->update($recommendation, [
            'name' => data_get($personData, 'name'),
            'statusCode' => data_get($personData, 'statusCode'),
            'registration' => data_get($personData, 'registration'),
            'pointOfSaleId' => data_get($personData, 'pointOfSaleId')
        ]);
    }

    public function getRecommendationImportableType($request, $importType)
    {
        $importable = ImportableFactory::make($importType);
        $engine     = new ImportEngine($importable);
        $file       = $request->file('file');
        return $engine->process($file);
    }
}
