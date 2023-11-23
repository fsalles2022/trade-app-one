<?php

declare(strict_types=1);

namespace TimBR\Http\Controllers;

use TimBR\Http\Requests\BrScanAuthenticateIdFormRequest;
use TimBR\Http\Requests\BrScanGenerateAuthenticateLinkFormRequest;
use TimBR\Http\Requests\BrScanServiceTransactionFormRequest;
use TimBR\Http\Resources\GenerateAuthenticateLinkResource;
use TimBR\Http\Resources\GenerateSaleForTermSignatureResource;
use TimBR\Http\Resources\GetAuthenticateStatusResource;
use TimBR\Http\Resources\GetSaleTermStatusResource;
use TimBR\Services\BrScanService;
use TradeAppOne\Http\Controllers\Controller;

class BrScanController extends Controller
{
    /** @var BrScanService */
    private $brScanService;

    public function __construct(BrScanService $brScanService)
    {
        $this->brScanService = $brScanService;
    }

    public function generateAuthenticateLink(BrScanGenerateAuthenticateLinkFormRequest $request): GenerateAuthenticateLinkResource
    {
        return new GenerateAuthenticateLinkResource(
            $this->brScanService->generateAuthenticateLink(
                (string) $request->input('customer.cpf'),
                (string) $request->input('customer.email'),
                (string) $request->input('customer.phone'),
                (int) $request->input('pointOfSaleId')
            )
        );
    }

    public function getAuthenticateStatus(BrScanAuthenticateIdFormRequest $request): GetAuthenticateStatusResource
    {
        return new GetAuthenticateStatusResource(
            $this->brScanService->getAuthenticateStatus((int) $request->input('authenticateId'))
        );
    }

    public function generateSaleTermForSignature(BrScanServiceTransactionFormRequest $request): GenerateSaleForTermSignatureResource
    {
        return new GenerateSaleForTermSignatureResource(
            $this->brScanService->generateSaleTermForSignature((string) $request->input('serviceTransaction'))
        );
    }

    public function getSaleTermStatus(BrScanAuthenticateIdFormRequest $request): GetSaleTermStatusResource
    {
        return new GetSaleTermStatusResource(
            $this->brScanService->getSaleTermStatus((int) $request->input('authenticateId'))
        );
    }
}
