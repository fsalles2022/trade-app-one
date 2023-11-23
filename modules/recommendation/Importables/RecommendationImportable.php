<?php

namespace Recommendation\Importables;

use Illuminate\Database\Eloquent\Model;
use Recommendation\Models\Recommendation;
use Recommendation\Services\RecommendationService;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableInterface;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class RecommendationImportable implements ImportableInterface
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function getExample(Recommendation $recommendation = null, string $cnpj = null): array
    {
        return [
            $recommendation->name ?? 'Joao Da Silva',
            $recommendation->statusCode ?? 'ACTIVE',
            $recommendation->registration ?? '102030',
            $cnpj ?? '22696923000162',
        ];
    }

    public function getColumns(): array
    {
        return [
            'name'              => trans('recommendation::importables.recommendation.name'),
            'statusCode'        => trans('recommendation::importables.recommendation.statusCode'),
            'registration'      => trans('recommendation::importables.recommendation.registration'),
            'pointOfSaleId'     => trans('recommendation::importables.recommendation.pointOfSaleId')
        ];
    }

    public function processLine($line)
    {
        $registration = data_get($line, 'registration');
        $registry     = $this->recommendationService->getRecommendationByRegistration($registration);

        return $registry === null
            ? $this->createNewRecommendation($line)
            : $this->updateRecommendation($registry, $line);
    }

    private function createNewRecommendation(array $line): Model
    {
        $personData = $this->adapterLine($line);
        return $this->recommendationService->createRecommendation($personData);
    }

    private function updateRecommendation(Recommendation $recommendation, array $line): Model
    {
        $personData = $this->adapterLine($line);
        return $this->recommendationService->updateRecommendation($recommendation, $personData);
    }

    private function adapterLine(array $line)
    {
        $name         = data_get($line, 'name');
        $statusCode   = data_get($line, 'statusCode');
        $registration = data_get($line, 'registration');
        $cnpj         = data_get($line, 'pointOfSaleId');

        $pointOfSaleId = PointOfSale::where('cnpj', $cnpj)->get()->first();

        return array_filter([
            'name'          => strtoupper($name),
            'statusCode'    => strtoupper($statusCode),
            'registration'  => $registration,
            'pointOfSaleId' => data_get($pointOfSaleId, 'id', 0)
        ]);
    }

    public function getType()
    {
        return Importables::RECOMMENDATIONS;
    }
}
