<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Domain\Importables\UserImportable;
use TradeAppOne\Domain\Importables\UserImportableDelete;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Http\Requests\ImportFormRequest;

class UsersImportController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function import(ImportFormRequest $request)
    {
        hasPermissionOrAbort(ImportablePermission::getFullName(ImportablePermission::USER));

        $errors = $this->userService->getUserImportableType($request, Importables::USERS);

        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getImportModel(): \League\Csv\Writer
    {
        $userImportable = resolve(UserImportable::class);
        $columns        = array_values($userImportable->getColumns());
        $lines          = $userImportable->getExample();
        return CsvHelper::arrayToCsv([$columns, $lines]);
    }

    public function importDelete(ImportFormRequest $request)
    {
        hasPermissionOrAbort(ImportablePermission::getFullName(Importables::USERS_DELETE));

        $errors = $this->userService->getUserImportableType($request, Importables::USERS_DELETE);

        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getImportModelDelete(): \League\Csv\Writer
    {
        $userImportDelete = resolve(UserImportableDelete::class);
        $columns          = array_values($userImportDelete->getColumns());
        $lines            = $userImportDelete->getExample();

        return CsvHelper::arrayToCsv([$columns, $lines]);
    }
}
