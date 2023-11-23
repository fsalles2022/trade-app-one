<?php

namespace Buyback\Http\Controllers;

use Buyback\Exportables\Sales\BuybackExport;
use Illuminate\Http\Request;
use Reports\Http\Requests\DefaultAnalyticalCriteriaFormRequest;
use TradeAppOne\Http\Controllers\Controller;

class BuybackExportsController extends Controller
{
    protected $export;

    public function __construct(BuybackExport $buybackExport)
    {
        $this->export = $buybackExport;
    }

    public function export(DefaultAnalyticalCriteriaFormRequest $request)
    {
        return $this->export->extractAnalytical($request->all());
    }

    public function unified(Request $request)
    {
        $user = $request->user();
        return $this->export->extractUnified($user);
    }
}
