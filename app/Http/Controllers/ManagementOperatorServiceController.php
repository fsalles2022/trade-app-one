<?php

namespace TradeAppOne\Http\Controllers\Management;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Importables\ServicesImportable;
use TradeAppOne\Domain\Services\OperatorServiceService;
use TradeAppOne\Http\Controllers\Controller;

class ManagementOperatorServiceController extends Controller
{
    protected $operatorService;

    public function __construct(OperatorServiceService $operatorService)
    {
        $this->operatorService = $operatorService;
    }

    public function getAllServices(): JsonResponse
    {
        $list = $this->operatorService->getAllServices();
        return response()->json($list, Response::HTTP_OK);
    }
    
    public function getAllOptions(): JsonResponse
    {
        return response()->json(
            $this->operatorService->getAllServiceOptions(),
            Response::HTTP_OK
        );
    }

    public function exportAllOptions(): StreamedResponse
    {
        return CsvHelper::exportDataToCsvFile(
            $this->operatorService->adapterServiceOptionsToExport(),
            'service-options'
        );
    }

    public function getServicesAndOptionsByNetwork(int $networkId): JsonResponse
    {
        $list = $this->operatorService->getServicesAndOptionsByAttribution(
            $networkId,
            'networkId'
        );

        return response()->json($list, Response::HTTP_OK);
    }

    public function getServicesAndOptionsByPointOfSale(int $pointOfSaleId): JsonResponse
    {
        $list = $this->operatorService->getServicesAndOptionsByAttribution(
            $pointOfSaleId,
            'pointOfSaleId'
        );

        return response()->json($list, Response::HTTP_OK);
    }

    public function getImportModel(): Writer
    {
        return ServicesImportable::buildExample();
    }

    public function postImportEnableServices(Request $request)
    {
        $importable = ImportableFactory::make(Importables::SERVICES);
        $process    = (new ImportEngine($importable))->process($request->file('file'));

        return $process ?: response()->json([
            'message' => trans('messages.default_success')
        ], Response::HTTP_CREATED);
    }
}
