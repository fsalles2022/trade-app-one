<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Policies\Authorizations;
use TradeAppOne\Domain\Services\ServiceService;
use TradeAppOne\Http\Requests\EditStatusFormRequest;
use TradeAppOne\Http\Requests\UpdateAvailableServicesRequest;

class ServiceController extends Controller
{
    /**
     * @var ServiceService
     */
    protected $serviceService;
    protected $authorizations;

    public function __construct(ServiceService $serviceService, Authorizations $authorizations)
    {
        $this->serviceService = $serviceService;
        $this->authorizations = $authorizations;
    }

    public function statusListing()
    {
        $statusConst = ConstantHelper::getAllConstants(ServiceStatus::class);
        $status      = [];

        foreach ($statusConst as $key => $value) {
            $status[] = [
                'slug'  => $key,
                'label' => trans("status.$key")
            ];
        }

        $toStatus['analytical'] = collect($status)->whereIn('slug', [
            ServiceStatus::ACCEPTED,
            ServiceStatus::APPROVED,
            ServiceStatus::CANCELED
        ])->values();
        $toStatus['all']        = $status;

        return response()->json($toStatus, Response::HTTP_OK);
    }

    public function editStatusByContext(EditStatusFormRequest $editStatusFormRequest)
    {
        hasPermissionOrAbort(SalePermission::getFullName(PermissionActions::EDIT_STATUS));

        $editStatusFormRequest->validated();
        $status             = $editStatusFormRequest->get('status');
        $serviceTransaction = $editStatusFormRequest->get('serviceTransaction');

        $statusChanged = $this->serviceService->editStatusByContext($serviceTransaction, $status);

        if ($statusChanged) {
            $this->response['message'] = trans('exceptions.service.status_change_success');
            return response()->json($this->response, Response::HTTP_CREATED);
        }

        $this->response['message'] = trans('exceptions.service.status_change_error');
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function update(EditStatusFormRequest $editStatusFormRequest)
    {
        hasPermissionOrAbort(SalePermission::getFullName(PermissionActions::EDIT));
        $data               = $editStatusFormRequest->validated();
        $serviceTransaction = $editStatusFormRequest->get('serviceTransaction');

        $statusChanged = $this->serviceService->update($serviceTransaction, $data);

        if ($statusChanged) {
            $this->response['message'] = trans('exceptions.service.change_success');
            return response()->json($this->response, Response::HTTP_CREATED);
        }

        $this->response['message'] = trans('exceptions.service.status_change_error');
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function updateAvailableServices(
        UpdateAvailableServicesRequest $request
    ): JsonResponse {
        $data = $request->validated();

        if ($this->serviceService->UpdateAvailableServices($data)) {
            $this->response['message'] = trans('exceptions.service.change_success');
            return response()->json($this->response, Response::HTTP_CREATED);
        }

        $this->response['message'] = trans('exceptions.service.status_change_error');
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
