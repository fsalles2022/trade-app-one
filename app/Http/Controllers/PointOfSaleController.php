<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Importables\PointOfSaleImportable;
use TradeAppOne\Domain\Services\PointOfSaleIntegrationService;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Facades\UserPolicies;
use TradeAppOne\Http\Requests\PointOfSaleFormRequest;
use TradeAppOne\Policies\PointOfSalePolicy;

class PointOfSaleController extends Controller
{
    private $pointOfSaleService;
    private $pointOfSalePolicy;
    private $pointOfSaleIntegration;

    public function __construct(
        PointOfSaleService $pointOfSaleService,
        PointOfSalePolicy $pointOfSalePolicy,
        PointOfSaleIntegrationService $pointOfSaleIntegration
    ) {
        $this->pointOfSaleService     = $pointOfSaleService;
        $this->pointOfSalePolicy      = $pointOfSalePolicy;
        $this->pointOfSaleIntegration = $pointOfSaleIntegration;
    }

    public function index(PointOfSaleFormRequest $request)
    {
        $pointsOfSale = $request->has('without-paginate') ?
            $this->pointOfSaleService->filterWithoutPaginate($request->validated())
            : $this->pointOfSaleService->filter($request->validated());

        return response()->json($pointsOfSale, Response::HTTP_OK);
    }

    public function show($cnpj, Request $request)
    {
        $pointOfSale = UserPolicies::hasAuthorizationUnderPointOfSale($cnpj)
            ->getPointsOfSaleAuthorized()
            ->firstWhere('cnpj', '=', $cnpj);

        return response()->json($pointOfSale->load('hierarchy:id,slug,label'), Response::HTTP_OK);
    }

    public function store(PointOfSaleFormRequest $request)
    {
        $user       = $request->user();
        $attributes = $request->all();

        $this->pointOfSalePolicy->create($user, $attributes, null);

        $this->pointOfSaleService->create($attributes);
        $response['message'] = trans('messages.pointOfSale.pointOfSale_created');

        return response()->json($response, Response::HTTP_OK);
    }

    public function edit(PointOfSaleFormRequest $request, string $cnpj)
    {
        $user       = $request->user();
        $attributes = $request->all();

        $this->pointOfSalePolicy->edit($user, $attributes, $cnpj);
        $this->pointOfSaleService->update($attributes, $cnpj);
        $response['message'] = trans('messages.pointOfSale.pointOfSale_updated');

        return response()->json($response, Response::HTTP_OK);
    }

    public function postImport(Request $request)
    {
        $importable = ImportableFactory::make(Importables::POINTS_OF_SALE);
        $engine     = new ImportEngine($importable);
        $errors     = $engine->process($request->file('file'));
        if ($errors) {
            return $errors;
        }
        $this->response['message'] = trans('messages.default_success');

        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getImportModel(): \League\Csv\Writer
    {
        return PointOfSaleImportable::buildExample();
    }

    public function getUserPointOfSaleLogged()
    {
        $user          = auth()->user();
        $pointsOfSales = $this->pointOfSaleService->getUserPointOfSaleLogged($user);

        return response()->json($pointsOfSales, Response::HTTP_OK);
    }

    public function updateByIntegration(PointOfSaleFormRequest $request)
    {
        $status = $this->pointOfSaleIntegration->updateSivIntegration($request->validated());
        return response()->json($status, Response::HTTP_OK);
    }
}
