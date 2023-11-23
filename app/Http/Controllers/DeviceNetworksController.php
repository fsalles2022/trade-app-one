<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Http\Requests\ImportFormRequest;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\DevicesNetworkImportable;

class DeviceNetworksController extends Controller
{

    public function getImportModel()
    {
        $pointOfSaleImportable = new DevicesNetworkImportable();
        $columns               = array_values($pointOfSaleImportable->getColumns());
        $networkId             = Auth::user()->getNetwork()->id;
        $lines                 = $pointOfSaleImportable->getExample($networkId);

        return CsvHelper::arrayToCsv([$columns, $lines]);
    }

    public function postImport(ImportFormRequest $request)
    {
        $importableInstance = resolve(DevicesNetworkImportable::class);
        $engine             = new ImportEngine($importableInstance);

        $file = $request->file('file');

        $errors = $engine->process($file);

        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');
        return response()->json($this->response, Response::HTTP_CREATED);
    }
}
