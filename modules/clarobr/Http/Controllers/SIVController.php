<?php

namespace ClaroBR\Http\Controllers;

use ClaroBR\Adapters\AutomaticRegistrationResponseAdapter;
use ClaroBR\Connection\SivAuth;
use ClaroBR\Exceptions\PlansNotFoundException;
use ClaroBR\Http\Requests\AnalyzeAuthenticateFormRequest;
use ClaroBR\Http\Requests\ClaroBrProductsFormRequest;
use ClaroBR\Http\Requests\CredentialsFormRequest;
use ClaroBR\Http\Requests\CreditAnalysisFormRequest;
use ClaroBR\Http\Requests\DevicesRebateFormRequest;
use ClaroBR\Http\Requests\RebateFormRequest;
use ClaroBR\Http\Requests\SaveAuthenticateFormRequest;
use ClaroBR\Http\Requests\SivAutomaticRegistrationFormRequest;
use ClaroBR\Http\Requests\SivCheckAutomaticRegistrationStatusFormRequest;
use ClaroBR\Http\Requests\SivFormRequest;
use ClaroBR\Jobs\ProcessAutomaticRegistration;
use ClaroBR\Services\SivAutomaticRegistrationService;
use ClaroBR\Services\SivSaleAssistance;
use ClaroBR\Services\SivService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Integrators\Adapters\ClaroListSaleAdapter;
use TradeAppOne\Http\Controllers\Controller;
use ClaroBR\Http\Requests\ClaroSalesListFormRequest;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SalePaginatedRepository;
use TradeAppOne\Domain\Services\SaleService;

class SIVController extends Controller
{
    protected $sivSaleAssistance;
    protected $sivService;
    protected $sivAutomaticRegistrationService;
    protected $sivAuth;
    protected $saleService;

    public function __construct(
        SivSaleAssistance $sivSaleAssistance,
        SivService $sivService,
        SivAutomaticRegistrationService $sivAutomaticRegistrationService,
        SivAuth $sivAuth,
        SaleService $saleService
    ) {
        $this->sivSaleAssistance               = $sivSaleAssistance;
        $this->sivService                      = $sivService;
        $this->sivAutomaticRegistrationService = $sivAutomaticRegistrationService;
        $this->sivAuth                         = $sivAuth;
        $this->saleService                     = $saleService;
    }

    public function utilsForCreateSale(Request $request)
    {
        if ($request->filter) {
            return $this->sivService->utilsForCreateSale($request->filter);
        }
        return $this->sivService->utilsForCreateSale();
    }

    public function plans(Request $request)
    {
        return $this->sivService->plans($request->all());
    }

    public function domains()
    {
        return $this->sivService->domains();
    }

    public function products(ClaroBrProductsFormRequest $request)
    {
        $options     = $request->validated();
        $plansMapped = $this->sivService->products($options);

        if ($plansMapped->isEmpty()) {
            throw new PlansNotFoundException();
        }
        return $plansMapped;
    }

    public function creditAnalysis(CreditAnalysisFormRequest $request): JsonResponse
    {
        return $this->sivService->creditAnalysis($request->validated());
    }

    public function analiseAuthenticate(AnalyzeAuthenticateFormRequest $request): JsonResponse
    {
        $response = $this->sivService->analiseAuthenticate($request->validated());
        return response()->json($response->toArray(), Response::HTTP_OK);
    }

    public function statusAuthenticate(AnalyzeAuthenticateFormRequest $request): JsonResponse
    {
        $response = $this->sivService->statusAuthenticate($request->validated());
        return response()->json($response->toArray(), Response::HTTP_OK);
    }

    public function saveStatusAuthenticate(SaveAuthenticateFormRequest $request): JsonResponse
    {
        $response = $this->sivService->saveStatus($request->validated());
        return response()->json($response->toArray(), Response::HTTP_OK);
    }

    public function residential(): array
    {
        return $this->sivService->residentialFlow();
    }

    public function saveCredentials(CredentialsFormRequest $request)
    {
        if ($this->sivService->saveCredentials($request->cpf, $request->password)) {
            $messages = ['messages' => trans('messages.credentials_third_party_success')];
            return response($messages, Response::HTTP_ACCEPTED);
        }
        $messages = ['messages' => trans('messages.credentials_third_party_failed')];
        return response($messages, Response::HTTP_PRECONDITION_REQUIRED);
    }

    public function rebate(RebateFormRequest $request)
    {
        $user = $request->user();
        return $this->sivService->rebate($request->all(), $user);
    }

    public function simulateRebate(DevicesRebateFormRequest $request)
    {
        $deviceSlug = data_get($request->validated(), 'device.model');
        return $this->sivService->rebateDevices($deviceSlug, $request->user());
    }

    public function promoterAuth(SivFormRequest $request)
    {
        $response = $this->sivAuth->promoter($request->validated());
        $token    = data_get($response, 'data.token');

        return response()->json($response, Response::HTTP_OK)
            ->withHeaders(['Authorization' => "Bearer {$token}"]);
    }

    public function devices()
    {
        $user = Auth::user();
        return $this->sivService->devices($user);
    }

    public function logSale(Request $request)
    {
        if ($this->sivSaleAssistance->logSale($request->all())) {
            $messages = ['messages' => trans('messages.default_success')];
            return response($messages, Response::HTTP_ACCEPTED);
        }
        $messages = ['messages' => trans('messages.default')];
        return response($messages, Response::HTTP_PRECONDITION_REQUIRED);
    }

    public function userLines(CreditAnalysisFormRequest $request): JsonResponse
    {
        $cpf   = $request->get('cpf');
        $lines = $this->sivService->userLines($cpf);

        return response()->json($lines, Response::HTTP_OK);
    }

    public function availableIccids(string $prefix): JsonResponse
    {
        $response = $this->sivService->availableIccids($prefix);
        return response()->json(['data' => $response['body']], $response['code']);
    }

    public function automaticRegistration(SivAutomaticRegistrationFormRequest $request): JsonResponse
    {
        $requestInput = $request->validated();

        ProcessAutomaticRegistration::dispatch($requestInput);

        return response()->json(AutomaticRegistrationResponseAdapter::adaptReceivedSuccess($requestInput), Response::HTTP_CREATED);
    }

    public function checkAutomaticRegistrationStatus(SivCheckAutomaticRegistrationStatusFormRequest $request): JsonResponse
    {
        return response()->json(
            $this->sivAutomaticRegistrationService->checkAutomaticRegistrationStatus($request->get('protocol')),
            Response::HTTP_OK
        );
    }

    public function listClaroSales(ClaroSalesListFormRequest $request): JsonResponse
    {
        $user                          = $request->user();
        $page                          = $request->get('page', 1);
        $perPage                       = $request->get('per_page', SalePaginatedRepository::QUANTITY_PER_PAGE);
        $requestParameters             = $request->validated();
        $requestParameters['operator'] = Operations::CLARO;
        $list                          = $this->saleService->filterByContext($user, $requestParameters, $page, $perPage);

        $list->setCollection(ClaroListSaleAdapter::adapt($list->getCollection()));

        return response()
            ->json($list->toArray(), Response::HTTP_OK, [], JSON_PRESERVE_ZERO_FRACTION);
    }
}
