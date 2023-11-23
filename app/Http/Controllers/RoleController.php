<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Adapters\MergeRoleWithPermissions;
use TradeAppOne\Domain\Adapters\NetworksRolesAdapter;
use TradeAppOne\Domain\Components\Permissions\PermissionsWrapper;
use TradeAppOne\Domain\Exportables\RoleExportable;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\RoleService;
use TradeAppOne\Http\Requests\RoleFormRequest;

class RoleController extends Controller
{
    protected $roleService;
    protected $hierarchyService;

    public function __construct(RoleService $roleService, HierarchyService $hierarchyService)
    {
        $this->roleService      = $roleService;
        $this->hierarchyService = $hierarchyService;
    }

    public function list(Request $request)
    {
        return $this->roleService->all(Auth::user(), $request->all());
    }

    public function index(RoleFormRequest $request)
    {
        return $this->roleService->all(Auth::user(), $request->validated());
    }

    public function show($id)
    {
        $user = Auth::user();
        $role = $this->roleService->show($id, $user);

        $response = (new MergeRoleWithPermissions(collect([$role])))->adapt();
        return response()->json($response, Response::HTTP_OK);
    }

    public function store(RoleFormRequest $request)
    {
        $userAuth = $request->user();
        $data     = $request->validated();

        if ($userAuth->can('defineParent', Role::class)) {
            $data['parent'] = data_get($data, 'parent', $userAuth->role->id);
        } else {
            $data['parent'] = $userAuth->role->id;
        }

        if ($userAuth->can('createRole', [Role::class, $data])) {
            if ($this->roleService->create($data, $userAuth)) {
                $response['message'] = trans('messages.role.created');
                return response()->json($response, Response::HTTP_CREATED);
            }
        }

        $this->response['message'] = trans('messages.role.no_permission');
        return response()->json($this->response, Response::HTTP_UNAUTHORIZED);
    }

    public function edit($id, RoleFormRequest $request)
    {
        $userAuth = $request->user();
        $data     = $request->validated();

        if ($userAuth->can('editRole', [Role::class, $id, $data])) {
            if ($this->roleService->update($id, $data, $userAuth)) {
                $response['message'] = trans('messages.role.edited');
                return response()->json($response, Response::HTTP_OK);
            }
        }

        $response['message'] = trans('messages.role.no_permission');
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

    public function export(): \League\Csv\Writer
    {
        $user  = \auth()->user();
        $roles = $this->roleService->rolesThatUserHasAuthority($user)->load('network');

        return (new RoleExportable($roles))->export();
    }

    public function getUserRoleLogged()
    {
        $userAuth             = Auth::user();
        $roles                = $this->roleService->rolesThatUserHasAuthority($userAuth);
        $rolesWithPermissions = (new MergeRoleWithPermissions($roles))->adapt();
        return response()->json($rolesWithPermissions, Response::HTTP_OK);
    }

    public function getPermissionsUserLogged()
    {
        $userAuth    = Auth::user();
        $permissions = $userAuth->role->stringPermissions;

        return PermissionsWrapper::groupPermissionsByModule($permissions);
    }

    public function rolesByNetwork()
    {
        $userAuth          = Auth::user();
        $networks          = $this->hierarchyService->getNetworksThatBelongsToUser($userAuth);
        $roles             = $this->roleService->rolesThatUserHasAuthority($userAuth);
        $networksWithRoles = (new NetworksRolesAdapter($roles, $networks))->adapt();

        return response()->json($networksWithRoles, Response::HTTP_OK);
    }
}
