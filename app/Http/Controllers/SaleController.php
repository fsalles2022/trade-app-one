<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Csv\Writer;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SalePaginatedRepository;
use TradeAppOne\Domain\Services\Cancel\CancelServicesService;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Domain\Services\ServiceService;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;
use TradeAppOne\Http\Requests\ActivationFormRequest;
use TradeAppOne\Http\Requests\OptionalSaleStepsRequest;
use TradeAppOne\Http\Requests\SaleFormRequest;
use TradeAppOne\Http\Requests\PreSaleFormRequest;
use TradeAppOne\Http\Requests\SaleListFormRequest;
use TradeAppOne\Http\Requests\SaleListIntegratorsFormRequest;
use TradeAppOne\Http\Resources\SaleResponseResource;

class SaleController extends Controller
{
    protected $saleService;
    protected $serviceService;
    private $cancelService;

    public function __construct(
        SaleService $saleService,
        ServiceService $serviceService,
        CancelServicesService $cancelService
    ) {
        $this->saleService    = $saleService;
        $this->serviceService = $serviceService;
        $this->cancelService  = $cancelService;
    }

    public function index(SaleListFormRequest $request)
    {
        $user                     = $request->user();
        $salePermissions          = $user->role->permissions[SubSystemEnum::API][SalePermission::NAME] ?? [];
        $hasListBuybackPermission = in_array(PermissionActions::VIEW_ONLY_TRADE_IN, $salePermissions);

        $page    = $request->get('page', 1);
        $perPage = $request->get('per_page', SalePaginatedRepository::QUANTITY_PER_PAGE);

        if ($hasListBuybackPermission) {
            $list = $this->saleService->filterByBuyback($user, $request->validated(), $page);
        } else {
            $list = $this->saleService->filterByContext($user, $request->validated(), $page, $perPage);
        }

        return response()
            ->json($list->toArray(), Response::HTTP_OK, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function integratorsIndex(SaleListIntegratorsFormRequest $request)
    {
        $page = $request->get('page', 1);
        $user = $request->user();
        $list = $this->saleService->filterToIntegrators($user, $request->validated(), $page);

        $list = SaleResponseResource::adapt($list);
        return response()->json($list, Response::HTTP_OK);
    }

    public function postSaveSale(SaleFormRequest $request)
    {
        $associateUserId = $request->get('associateUserId');
        $captcha         = $request->get('captcha', '');

        $captchaData = json_decode(base64_decode($captcha), true);
        $captchaCode = is_array($captchaData) && isset($captchaData['code']) ? (string) $captchaData['code'] : '';
        $captchaKey  = is_array($captchaData) && isset($captchaData['key']) ? (string) $captchaData['key'] : '';

        $user = $associateUserId
            ? User::find($associateUserId)
            : $request->user();

        $source = $request->headers->get('client');
        $sale   = $this->saleService->new($captchaCode, $captchaKey, $source, $user, $request->services, $request->pointOfSale);

        if ($sale) {
            $body['messages']     = trans('messages.sale_saved');
            $body['data']['sale'] = $sale;

            return response($body, Response::HTTP_CREATED);
        }

        $body['messages'] = trans('messages.sale_not_saved');

        return response($body, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function putActivateSale(ActivationFormRequest $request)
    {
        try {
            return $this->saleService->integrateService($request->all());
        } catch (BusinessRuleExceptions $e) {
            $this->response['error'] = $e->getError();

            return response()->json($this->response, $e->getHttpStatus());
        }
    }

    public function putUpdatePreSale(PreSaleFormRequest $request): JsonResponse
    {
        try {
            $updatedPreSale         = $this->saleService->updatePreSale($request->validated());
            $this->response['data'] = ['preSaleUpdated' => $updatedPreSale];

            if ($updatedPreSale) {
                $this->response['message'] = trans('messages.preSale.success');
                return response()->json($this->response, Response::HTTP_OK);
            }

            $this->response['message'] = trans('messages.preSale.error');
            return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        } catch (BusinessRuleExceptions $exception) {
            $this->response['error'] = trans('messages.preSale.notFound');
            return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        }
    }

    public function putContest(ActivationFormRequest $request)
    {
        $result = $this->serviceService->contestService($request->all());

        if ($result instanceof Service) {
            return [
                'message' => trans('messages.contest.success', ['status' => trans('status.' . $result->status)])
            ];
        }

        if (is_array($result)) {
            $message = empty(data_get($result, 'message')) ?
                trans('status.' . data_get($result, 'service.status'))
                : data_get($result, 'message');
            return [
                'message' => trans('messages.contest.success', ['status' => $message])
            ];
        }

        return $result;
    }

    public function putCancel(ActivationFormRequest $request)
    {
        hasPermissionOrAbort(SalePermission::getFullName(SalePermission::CANCEL));
        $message = $this->cancelService->cancel($request->user(), $request->get('serviceTransaction'));

        return response()->json(['message' => $message], Response::HTTP_OK);
    }

    public function saleOptions(OptionalSaleStepsRequest $request)
    {
        $services = $this->saleService->options($request->validated(), $request->user());
        return response()->json($services, Response::HTTP_OK);
    }

    public function getModelOiResidentialSale(): Writer
    {
        return $this->saleService->getModelCsv();
    }

    /** @return mixed */
    public function postImportOiResidentialSale(Request $request)
    {
        $error = $this->saleService->importResidentialSaleCsv($request->file('file'));
        if (empty($error) === false) {
            return $error;
        }

        return response()->json(
            $this->response['message'] = trans('messages.default_success'),
            Response::HTTP_CREATED
        );
    }
}
