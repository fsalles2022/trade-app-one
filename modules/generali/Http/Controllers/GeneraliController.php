<?php


namespace Generali\Http\Controllers;

use Gateway\Components\Interest;
use Generali\Http\Requests\GeneraliFormRequest;
use Generali\Services\GeneraliService;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Http\Controllers\Controller;

class GeneraliController extends Controller
{
    protected $generaliService;
    protected $saleRepository;

    public function __construct(GeneraliService $generaliService, SaleRepository $saleRepository)
    {
        $this->generaliService = $generaliService;
        $this->saleRepository  = $saleRepository;
    }

    public function calcRefund(GeneraliFormRequest $request): array
    {
        return $this->generaliService->calcRefund($request->get('reference'));
    }

    public function eligibility(GeneraliFormRequest $request): array
    {
        return $this->generaliService->eligibility($request->validated());
    }

    public function products(): array
    {
        return $this->generaliService->products();
    }

    public function plans(GeneraliFormRequest $request): array
    {
        return $this->generaliService->plans($request->validated());
    }

    public function coverage(int $id): array
    {
        return $this->generaliService->coverage($id);
    }

    public function ticket(GeneraliFormRequest $request)
    {
        $serviceTransaction = data_get($request->validated(), 'serviceTransaction');

        return $this->generaliService->insuranceTicket($serviceTransaction);
    }

    public function interest(GeneraliFormRequest $request)
    {
        $serviceTransaction = $request->get('serviceTransaction');

        $price = $this->saleRepository->findInSale($serviceTransaction)->price;
        return response()->json(Interest::all($price), Response::HTTP_OK);
    }

    public function updateInsurance(GeneraliFormRequest $request): void
    {
        $this->generaliService->updateInsuranceFromGateway($request->validated());
    }
}
