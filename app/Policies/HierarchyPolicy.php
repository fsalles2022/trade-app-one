<?php


namespace TradeAppOne\Policies;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\HierarchyPermissions;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Facades\UserPolicies;
use TradeAppOne\Http\Requests\HierarchyFormRequest;

class HierarchyPolicy
{
    protected $request;
    protected $hierarchyRepository;

    public function __construct(HierarchyRepository $hierarchyRepository)
    {
        $this->hierarchyRepository = $hierarchyRepository;
    }

    public function create(HierarchyFormRequest $request): Hierarchy
    {
        $businessRules = UserPolicies::setUser($request->user());
        $permission    = HierarchyPermissions::getFullName(PermissionActions::CREATE);

        $data      = $request->validated();
        $network   = data_get($data, 'networkSlug');
        $hierarchy = data_get($data, 'parent');

        $businessRules
            ->hasPermission($permission)
            ->hasAuthorizationUnderNetwork($network)
            ->hasAuthorizationUnderHierarchy($hierarchy);

        return $this->hierarchyRepository->create($this->adapter($data));
    }

    public function adapter(array $attributes): array
    {
        $network   = NetworkRepository::findOneBy('slug', $attributes['networkSlug'])->first();
        $hierarchy = $this->hierarchyRepository->findOneBy('slug', data_get($attributes, 'parent'));

        return [
            'label'     => data_get($attributes, 'label'),
            'parent'    => $hierarchy->id,
            'slug'      => str_slug(data_get($attributes, 'slug')),
            'networkId' => $network->id
        ];
    }
}
