<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Services\ImportHistoryService;
use TradeAppOne\Http\Requests\ImportHistoryFormRequest;

class ImportHistoryController extends Controller
{
    protected $importHistoryService;

    public function __construct(ImportHistoryService $importHistoryService)
    {
        $this->importHistoryService = $importHistoryService;
    }

    public function getHistory(ImportHistoryFormRequest $request)
    {
        $user          = $request->user();
        $filters       = $request->validated();
        $importHistory = $this->importHistoryService->getHistory($user, $filters);
        return response()->json($importHistory, Response::HTTP_OK);
    }

    public function getFile(Request $request)
    {
        $user = $request->user();

        return $this->importHistoryService->getFile($request, $user);
    }
}
