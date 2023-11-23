<?php

namespace Core\HandBooks\Http\Controllers;

use Illuminate\Http\Response;
use TradeAppOne\Facades\UserPolicies;
use Core\HandBooks\Models\HandbookRequest;
use Core\HandBooks\Services\HandbookService;
use TradeAppOne\Http\Controllers\Controller;
use Core\HandBooks\Exceptions\HandbookExceptions;
use Core\HandBooks\Repositories\HandbookRepository;
use Core\HandBooks\Enumerators\HandbookPermissions;
use Core\HandBooks\Http\Requests\HandbookFormRequest;
use Core\HandBooks\Http\Resources\HandbookDomainsResource;
use Core\HandBooks\Http\Resources\HandbookListResource;
use Core\HandBooks\Http\Resources\HandbookShowResource;

class HandbookController extends Controller
{
    private $service;

    public function __construct(HandbookService $service)
    {
        $this->service = $service;
    }

    public function index(HandbookFormRequest $request)
    {
        $handbooks = HandbookListResource::list($request);
        return response()->json($handbooks, Response::HTTP_OK);
    }

    public function paginate(HandbookFormRequest $request)
    {
        $handbooks = HandbookListResource::paginate($request);
        return response()->json($handbooks, Response::HTTP_OK);
    }

    public function store(HandbookFormRequest $request)
    {
        $handbookRequest = (new HandbookRequest($request))->validateToCreate();
        $this->service->create($handbookRequest);

        return response()->json(
            trans('handbook::messages.created_success'),
            Response::HTTP_CREATED
        );
    }

    public function update(int $id, HandbookFormRequest $request)
    {
        $handbook = HandbookRepository::find($id);

        $handbookRequest = (new HandbookRequest($request))->validateToUpdate($handbook);
        $this->service->update($handbookRequest, $handbook);

        return response()->json(
            trans('handbook::messages.updated_success'),
            Response::HTTP_OK
        );
    }

    public function edit(int $id)
    {
        $handbook = HandbookRepository::find($id);

        UserPolicies::setUser(auth()->user())
            ->hasAuthorizationUnderUserAndMe($handbook->user);

        $adapted = HandbookShowResource::edit($handbook);
        return response()->json($adapted, Response::HTTP_OK);
    }

    public function show(int $id)
    {
        $handbook = HandbookRepository::find($id);

        $handbooks = HandbookRepository::filter(auth()->user())->get();

        if ($handbooks->contains('id', $handbook->id)) {
            return HandbookShowResource::show($handbook);
        }

        throw HandbookExceptions::hasNotAuthorizationUnderHandbook();
    }

    public function delete(int $id)
    {
        $handbook   = HandbookRepository::find($id);
        $permission = HandbookPermissions::getFullName(HandbookPermissions::DELETE);

        UserPolicies::setUser(auth()->user())
            ->hasPermission($permission)
            ->hasAuthorizationUnderUserAndMe($handbook->user);

        $handbook->delete();

        return response()->json(
            trans('handbook::messages.deleted_success'),
            Response::HTTP_OK
        );
    }

    public function domains()
    {
        $domains = HandbookDomainsResource::all(auth()->user());
        return response()->json($domains, Response::HTTP_OK);
    }
}
