<?php

declare(strict_types=1);

namespace Discount\Http\Controllers;

use Discount\Http\Requests\UpdateImeiFormRequest;
use Discount\Services\Input\AuthorizationUpdateImeiInput;
use Discount\Services\Input\GetSaleWithImeiInput;
use Discount\Services\Input\UpdateImeiServiceInput;
use Discount\Services\UpdateImeiService;
use Illuminate\Http\JsonResponse;
use TradeAppOne\Http\Controllers\Controller;

class UpdateImeiController extends Controller
{
    /** @var UpdateImeiService */
    private $updateImeiService;

    public function __construct(UpdateImeiService $updateImeiService)
    {
        $this->updateImeiService = $updateImeiService;
    }

    public function getImei(UpdateImeiFormRequest $updateImeiFormRequest): JsonResponse
    {
        $request = $updateImeiFormRequest->validated();

        $output = $this->updateImeiService->getInformationAboutSale(
            new GetSaleWithImeiInput(
                $request['cpf'] ?? null,
                $request['serviceTransaction'] ?? null
            )
        );

        return response()->json($output->jsonSerialize());
    }

    public function authorizeUpdateImei(UpdateImeiFormRequest $updateImeiFormRequest): JsonResponse
    {
        $request = $updateImeiFormRequest->validated();

        $output = $this->updateImeiService->authorize(
            new AuthorizationUpdateImeiInput(
                $request['login'] ?? null,
                $request['password'] ?? null,
                $request['serviceTransaction'] ?? null
            )
        );

        return response()->json($output->jsonSerialize());
    }

    public function updateImei(UpdateImeiFormRequest $updateImeiFormRequest): JsonResponse
    {
        $request = $updateImeiFormRequest->validated();

        $output = $this->updateImeiService->updateImeiInService(
            new UpdateImeiServiceInput(
                $request['token'] ?? null,
                $request['authorizerCpf'] ?? null,
                $request['serviceTransaction'] ?? null,
                $request['newImei'] ?? null,
                $request['oldImei'] ?? null,
                $request['customerCpf'] ?? null
            )
        );

        return response()->json($output);
    }
}
