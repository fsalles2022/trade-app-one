<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Services\UserReaderService;
use TradeAppOne\Exports\Operators\RegisterMailToNextel;
use TradeAppOne\Exports\Operators\RegisterMailToOi;
use TradeAppOne\Http\Requests\UserListFormRequest;
use TradeAppOne\Http\Requests\UsersExportFormRequest;

class UsersExportController extends Controller
{
    protected $readerService;

    public function __construct(UserReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    public function export(UserListFormRequest $request): string
    {
        return $this->readerService->exportUsers($request->validated())->getCsv();
    }
}
