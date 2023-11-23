<?php

declare(strict_types=1);

namespace Terms\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Terms\Http\Requests\TermFormRequest;
use Terms\Services\TermService;
use TradeAppOne\Http\Controllers\Controller;

class TermController extends Controller
{
    /** @var TermService */
    private $termService;

    public function __construct(TermService $termService)
    {
        $this->termService = $termService;
    }

    public function getTerm(TermFormRequest $request): JsonResponse
    {
        return response()->json(
            $this->termService->findTermService($request->validated()),
            Response::HTTP_OK
        );
    }

    public function checkTerm(TermFormRequest $request): JsonResponse
    {
        return response()->json(
            $this->termService->acceptedUserTerm($request->validated()),
            Response::HTTP_OK
        );
    }
}
