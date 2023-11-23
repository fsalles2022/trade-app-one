<?php

namespace TradeAppOne\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Http\Requests\NetworkFormRequest;
use TradeAppOne\Policies\NetworkPolicy;

class NetworkController extends Controller
{
    protected $networkService;
    protected $networkPolicy;

    public function __construct(NetworkService $networkService, NetworkPolicy $networkPolicy)
    {
        $this->networkService = $networkService;
        $this->networkPolicy  = $networkPolicy;
    }

    public function index(NetworkFormRequest $request)
    {
        $networks = $request->has('without-paginate') ?
            $this->networkService->filterWithoutPaginate($request->validated())
            : $this->networkService->filter($request->validated());

        return response()->json($networks, Response::HTTP_OK);
    }

    public function store(NetworkFormRequest $request)
    {
        $this->networkPolicy->create($request->validated());
        $response['message'] = trans('messages.network_create');

        return response()->json($response, Response::HTTP_CREATED);
    }
}
