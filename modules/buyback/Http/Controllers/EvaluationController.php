<?php

namespace Buyback\Http\Controllers;

use Buyback\Http\Requests\EvaluationsFormRequest;
use Buyback\Services\EvaluationService;
use Illuminate\Http\Request;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\EvaluationImportable;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Http\Controllers\Controller;
use Illuminate\Http\Response;

class EvaluationController extends Controller
{
    protected $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function import(Request $request)
    {
        $importable = ImportableFactory::make(Importables::EVALUATIONS);
        $engine     = new ImportEngine($importable);
        $errors     = $engine->process($request->file('file'));
        if ($errors) {
            return $errors;
        }
        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getImportModel()
    {
        $evaluationImportable = resolve(EvaluationImportable::class);
        $columns              = array_values($evaluationImportable->getColumns());
        $lines                = $evaluationImportable->getExample();
        return CsvHelper::arrayToCsv([$columns, $lines]);
    }

    public function getDevicesEvaluations(EvaluationsFormRequest $request)
    {
        $filters     = $request->validated();
        $evaluations = $this->evaluationService->devicesEvaluationsPaginated($filters);

        return response()->json($evaluations, Response::HTTP_OK);
    }

    public function export(EvaluationsFormRequest $request)
    {
        $filters = $request->validated();

        return $this->evaluationService->export($filters)->export();
    }
}
