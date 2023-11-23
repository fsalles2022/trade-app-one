<?php

declare(strict_types=1);

namespace Bulletin\Http\Controllers;

use Bulletin\Http\Requests\BulletinFormRequest;
use Bulletin\Models\Bulletin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Bulletin\Service\BulletinServices;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Http\Controllers\Controller;

class BulletinController extends Controller
{
    /** @var BulletinServices */
    private $bulletinServices;

    public function __construct(BulletinServices $bulletinServices)
    {
        $this->bulletinServices = $bulletinServices;
    }

    /** @return LengthAwarePaginator */
    public function index(): LengthAwarePaginator
    {
        return $this->bulletinServices->getBulletins()->paginate(10);
    }

    /**
     * @param BulletinFormRequest $bulletinFormRequest
     * @param Bulletin $bulletin
     * @return JsonResponse
     * @throws \Throwable
     */
    public function activate(BulletinFormRequest $bulletinFormRequest, Bulletin $bulletin): JsonResponse
    {
        $this->bulletinServices->changeActivationStatus($bulletinFormRequest->validated(), $bulletin);

        return response()->json([
            'message' => trans('bulletin::messages.activation_updated'),
        ], Response::HTTP_OK);
    }

    /**
     * @param Bulletin $bulletin
     * @return JsonResponse
     * @throws \Throwable
     */
    public function confirm(Bulletin $bulletin): JsonResponse
    {
        $this->bulletinServices->seen($bulletin);

        return response()->json([
            'message' => trans('bulletin::messages.activation_updated'),
        ], Response::HTTP_OK);
    }

    /**
     * @param BulletinFormRequest $bulletinFormRequest
     * @return JsonResponse
     */
    public function store(BulletinFormRequest $bulletinFormRequest): JsonResponse
    {
        return response()->json([
            'message' => trans('bulletin::messages.bulletin_store'),
            'data' => $this->bulletinServices->registerBulletins($bulletinFormRequest->validated())
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Bulletin $bulletin
     * @return Bulletin
     */
    public function show(Bulletin $bulletin): Bulletin
    {
        return $bulletin;
    }

    /**
     * @param BulletinFormRequest $bulletinFormRequest
     * @param Bulletin $bulletin
     * @return JsonResponse
     */
    public function update(BulletinFormRequest $bulletinFormRequest, Bulletin $bulletin): JsonResponse
    {
        return response()->json([
            'message' => trans('bulletin::messages.bulletin_update'),
            'data' => $this->bulletinServices->update($bulletinFormRequest->validated(), $bulletin)
        ], Response::HTTP_OK);
    }

    /**
     * @param Bulletin $bulletin
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Bulletin $bulletin): JsonResponse
    {
        return response()->json([
            'message' => trans('bulletin::messages.bulletin_destroy'),
            'data' => $this->bulletinServices->delete($bulletin)
        ], Response::HTTP_OK);
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getUserBulletin(): Collection
    {
        return $this->bulletinServices->bulletinByUser();
    }

    /** @return JsonResponse */
    public function filters(): JsonResponse
    {
        $filters = $this->bulletinServices->getFiltersByAuthUser();
        return response()->json($filters, Response::HTTP_OK);
    }
}
