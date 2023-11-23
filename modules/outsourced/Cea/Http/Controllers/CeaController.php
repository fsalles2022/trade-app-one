<?php

namespace Outsourced\Cea\Http\Controllers;

use Illuminate\Http\Response;
use Outsourced\Cea\Http\Requests\CeaFormRequest;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Http\Controllers\Controller;
use Outsourced\Cea\GiftCardConnection\CeaConnection;

class CeaController extends Controller
{
    public function importGiftCards(CeaFormRequest $request)
    {
        $file       = data_get($request->validated(), 'file');
        $importable = ImportableFactory::make(Importables::CEA_GIFT_CARDS);
        $engine     = new ImportEngine($importable);
        $process    = $engine->process($file);

        if ($process === null) {
            return response()->json(['message' => trans('cea::messages.importable_success')], Response::HTTP_CREATED);
        }

        return $process;
    }

    public function importExample()
    {
        $importable = ImportableFactory::make(Importables::CEA_GIFT_CARDS);
        return $importable->getExample();
    }

    public function activateGiftCard(CeaFormRequest $request)
    {
        $ceaConnection = resolve(CeaConnection::class);
        return $ceaConnection->activateGiftCard($request->cardNumber, $request->value, $request->partner, $request->customer);
    }
}
