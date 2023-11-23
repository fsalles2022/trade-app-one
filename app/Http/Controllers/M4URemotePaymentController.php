<?php
declare(strict_types=1);

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Services\RemotePaymentServices;

class M4URemotePaymentController extends Controller
{
    /**
     * @var RemotePaymentServices
     */
    private $remotePaymentServices;

    public function __construct(RemotePaymentServices $remotePaymentServices)
    {
        $this->remotePaymentServices = $remotePaymentServices;
    }

    public function index(string $token): JsonResponse
    {
        $service = $this->remotePaymentServices->findServicesByToken($token);
        return response()->json(['integratorPaymentURL' => $service->integratorPaymentURL], Response::HTTP_OK);
    }
}
