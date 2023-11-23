<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\DeviceOutSourcedImportable;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Http\Requests\ImportFormRequest;

class DeviceOutSourcedController
{
    public function importModel(Request $request, DeviceOutSourcedImportable $importable)
    {
        $columns     = array_values($importable->getColumns());
        $networkSlug = $request->user()->getNetwork()->slug;
        $lines       = $importable->getExample($networkSlug);

        return CsvHelper::arrayToCsv([$columns, $lines]);
    }

    public function exportData(Request $request, DeviceOutSourcedImportable $importable)
    {
        $columns   = array_values($importable->getColumns());
        $networkId = $request->user()->getNetwork()->id;
        $lines     = $importable->getExportData($networkId);


        return CsvHelper::arrayToCsv(array_merge([$columns], $lines));
    }

    public function import(ImportFormRequest $request, DeviceOutSourcedImportable $importable)
    {
        $importEngine = new ImportEngine($importable);
        $file         = $request->file('file');
        $errors       = $importEngine->process($file);

        if ($errors) {
            return $errors;
        }
        $response['message'] = trans('messages.default_success');
        return response()->json($response, Response::HTTP_CREATED);
    }
}
