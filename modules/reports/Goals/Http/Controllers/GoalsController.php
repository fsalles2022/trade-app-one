<?php

namespace Reports\Goals\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Reports\Goals\Importables\GoalImportable;
use Reports\Goals\Services\ExportImportGoalsService;
use Reports\Goals\Services\GoalService;
use Reports\Http\Requests\ExportGoalsFormRequest;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Permissions\GoalPermission;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\ImportFormRequest;

class GoalsController extends Controller
{
    protected $goalService;
    protected $exportImportGoalsService;

    public function __construct(GoalService $goalService, ExportImportGoalsService $exportImportGoalsService)
    {
        $this->goalService              = $goalService;
        $this->exportImportGoalsService = $exportImportGoalsService;
    }

    public function import(ImportFormRequest $request)
    {
        $this->authorize('importGoalOfPointOfSale');

        $file = $request->file('file');
        $user = $request->user();

        $errors = $this->exportImportGoalsService->import($file, $user);

        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function example()
    {
        $goalsTypes = auth()->user()->getNetwork()->goalsTypes;

        $importable = app()->makeWith(
            GoalImportable::class,
            [
                'goalsTypes' => $goalsTypes
            ]
        );

        $example = array_values($importable->getExample());
        return CsvHelper::arrayToCsv($example);
    }

    public function list(Request $request)
    {
        $goalService = resolve(GoalService::class);
        return $goalService->fetchWithContext($request->all());
    }

    public function export(ExportGoalsFormRequest $request)
    {
        $user             = $request->user();
        $permissionExport = GoalPermission::getFullName(GoalPermission::EXPORT);

        if ($user->hasPermission($permissionExport)) {
            return $this->exportImportGoalsService->export($user, $request->validated());
        }

        throw UserExceptions::userUnauthorized();
    }
}
