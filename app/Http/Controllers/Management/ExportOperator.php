<?php
namespace TradeAppOne\Http\Controllers\Management;

use Illuminate\Http\Request;
use TradeAppOne\Exports\Operators\Queries\Claro\ClaroCustCodes;
use TradeAppOne\Exports\Operators\Queries\Claro\ClaroUsers;
use TradeAppOne\Exports\Operators\Queries\Tim\TimCustCodes;
use TradeAppOne\Exports\Operators\Queries\Tim\TimUsers;
use TradeAppOne\Http\Controllers\Controller;

class ExportOperator extends Controller
{
    protected $export;
    protected $exportUsersClaro;
    protected $exportClaroCustCodes;

    public function __construct(
        TimCustCodes $timCustCodes,
        ClaroUsers $claroUsers,
        ClaroCustCodes $claroCustCodes
    ) {
        $this->export               = $timCustCodes;
        $this->exportUsersClaro     =  $claroUsers;
        $this->exportClaroCustCodes = $claroCustCodes;
    }

    public function usersTim(Request $request)
    {
        $export   = new TimUsers();
        $zip_file =  $export->export($request->all());
        return response()->download($zip_file)->deleteFileAfterSend();
    }

    public function pointsOfSaleTim(Request $request): \League\Csv\Writer
    {
        return $this->export->export($request->all());
    }

    public function usersClaro(Request $request): \League\Csv\Writer
    {
        return $this->exportUsersClaro->export($request->all());
    }

    public function pointsOfSaleClaro(Request $request): \League\Csv\Writer
    {
        return $this->exportClaroCustCodes->export($request->all());
    }
}
