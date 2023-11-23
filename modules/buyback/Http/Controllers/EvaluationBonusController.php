<?php

namespace Buyback\Http\Controllers;

use Buyback\Services\EvaluationBonusService;
use Illuminate\Http\Request;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\EvaluationBonusImportable;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Http\Controllers\Controller;
use Illuminate\Http\Response;

class EvaluationBonusController extends Controller
{
    protected $evaluationBonusService;

    public function __construct(EvaluationBonusService $evaluationBonusService)
    {
        $this->evaluationBonusService = $evaluationBonusService;
    }

    public function import(Request $request)
    {
        $importable = ImportableFactory::make(Importables::EVALUATIONS_BONUS);
        $engine     = new ImportEngine($importable);
        $errors     = $engine->process($request->file('file'));
        if ($errors) {
            return $errors;
        }
        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getImportModel(): Writer
    {
        $evaluationImportable = resolve(EvaluationBonusImportable::class);
        $columns              = array_values($evaluationImportable->getColumns());
        $lines                = $evaluationImportable->getExample();
        return CsvHelper::arrayToCsv([$columns, $lines]);
    }
}
