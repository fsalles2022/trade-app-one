<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Services\ImportableService;
use Illuminate\Http\Response;

class ImportableController extends Controller
{
    private $importableService;

    public function __construct(ImportableService $importableService)
    {
        $this->importableService = $importableService;
    }

    public function getDevices()
    {
        $user           = Auth::user();
        $devicesCsvFile = $this->importableService->getNetworkDevices($user);
        if ($devicesCsvFile) {
            return $devicesCsvFile;
        }
        $this->response['message'] = trans('buyback::exceptions.device_not_found.message');
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
