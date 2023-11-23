<?php

declare(strict_types=1);

namespace Core\PowerBi\Http\Controllers;

use Core\PowerBi\Connections\PowerBiConnection;
use Core\PowerBi\Constants\PowerBiDashboards;
use Core\PowerBi\Services\PowerBiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Http\Controllers\Controller;

class PowerBiController extends Controller
{
    /** @var PowerBiConnection */
    private $connection;

    /** @var PowerBiService */
    private $powerBiService;

    public function __construct(PowerBiConnection $connection, PowerBiService $powerBiService)
    {
        $this->connection     = $connection;
        $this->powerBiService = $powerBiService;
    }

    public function mcAfee():JsonResponse
    {
        $dashboard = $this->connection->getDashboard(PowerBiDashboards::MCAFEE);
        return response()->json($dashboard, Response::HTTP_OK);
    }

    public function lads(): JsonResponse
    {
        $dashboard = $this->connection->getDashboard(PowerBiDashboards::LADS);
        return response()->json($dashboard, Response::HTTP_OK);
    }

    public function telephony(): JsonResponse
    {
        return response()->json(
            $this->connection->getDashboard(PowerBiDashboards::TELEPHONY),
            Response::HTTP_OK
        );
    }

    public function management(): JsonResponse
    {
        $dashboard = $this->connection->getDashboard(PowerBiDashboards::MANAGEMENT);
        return response()->json($dashboard, Response::HTTP_OK);
    }

    public function insurance(): JsonResponse
    {
        $dashboard = $this->connection->getDashboard(PowerBiDashboards::INSURANCE);
        return response()->json($dashboard, Response::HTTP_OK);
    }

    public function tradeIn(): JsonResponse
    {
        $dashboard = $this->connection->getDashboard(PowerBiDashboards::TRADE_IN);
        return response()->json($dashboard, Response::HTTP_OK);
    }

    public function goalsCea(): JsonResponse
    {
        $dashboard = $this->connection->getDashboard(PowerBiDashboards::GOALS_CEA);
        return response()->json($dashboard, Response::HTTP_OK);
    }

    public function goalsRiachuelo(): JsonResponse
    {
        $riachueloGoalDash = $this->connection->getDashboard(PowerBiDashboards::GOALS_RIACHUELO);
        return response()->json($riachueloGoalDash, Response::HTTP_OK);
    }

    public function salesPernambucanas(): JsonResponse
    {
        $pernambucanasSalesDash = $this->connection->getDashboard(PowerBiDashboards::SALES_PERNAMBUCANAS);
        return response()->json($pernambucanasSalesDash, Response::HTTP_OK);
    }

    public function commissionTim(): JsonResponse
    {
        return response()->json(
            $this->connection->getDashboard(PowerBiDashboards::COMMISSION_TIM),
            Response::HTTP_OK
        );
    }

    public function claroMarketShare(): JsonResponse
    {
        return response()->json(
            $this->connection->getDashboard(PowerBiDashboards::CLARO_MARKET_SHARE),
            Response::HTTP_OK
        );
    }

    public function getFilters(Request $request):JsonResponse
    {
        $reportFilterName = $request->get('report');
        $filters          = $this->powerBiService->getFilters(Auth::user(), $reportFilterName);
        return response()->json($filters, Response::HTTP_OK);
    }
}
