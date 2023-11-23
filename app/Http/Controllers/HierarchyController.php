<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Exportables\HierarchyExportable;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Http\Requests\HierarchyFormRequest;
use TradeAppOne\Policies\HierarchyPolicy;

class HierarchyController extends Controller
{
    protected $hierarchyService;
    protected $hierarchyPolicy;

    public function __construct(HierarchyService $hierarchyService, HierarchyPolicy $hierarchyPolicy)
    {
        $this->hierarchyService = $hierarchyService;
        $this->hierarchyPolicy  = $hierarchyPolicy;
    }

    public function index(Request $request)
    {
        $hierarchies = $this->hierarchyService
            ->hierarchiesThatUserHasAuthority($request->user());

        return response()->json($hierarchies, Response::HTTP_OK);
    }

    public function store(HierarchyFormRequest $request)
    {
        $this->hierarchyPolicy->create($request);
        $response['message'] = trans('messages.hierarchy_create');

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function postList(HierarchyFormRequest $request)
    {
        $hierarchies = $this->hierarchyService
            ->hierarchiesThatUserHasAuthorityPaginated($request->user(), $request->validated());
        return response()->json($hierarchies, Response::HTTP_OK);
    }

    public function export(Request $request): \League\Csv\Writer
    {
        $hierarchies = $this->hierarchyService
            ->hierarchiesThatUserHasAuthority($request->user());

        return (new HierarchyExportable($hierarchies))->export();
    }
}
