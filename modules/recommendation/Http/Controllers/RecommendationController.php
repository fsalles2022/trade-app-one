<?php


namespace Recommendation\Http\Controllers;

use Illuminate\Http\Response;
use League\Csv\Writer;
use Recommendation\Http\Requests\RecommendationFormRequest;
use Recommendation\Importables\RecommendationImportable;
use Recommendation\Services\RecommendationService;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\ImportFormRequest;

class RecommendationController extends Controller
{
    protected $recommendationService;
    protected $saleRepository;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function getRecommendation(RecommendationFormRequest $request): array
    {
        return $this->recommendationService->indicated($request->validated());
    }

    public function import(ImportFormRequest $request)
    {
        $errors = $this->recommendationService->getRecommendationImportableType($request, Importables::RECOMMENDATIONS);

        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getImportModel(): Writer
    {
        $importable = resolve(RecommendationImportable::class);
        $columns    = array_values($importable->getColumns());
        $lines      = $importable->getExample();
        return CsvHelper::arrayToCsv([$columns, $lines]);
    }
}
