<?php

namespace TradeAppOne\Policies;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\NetworkPermission;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Facades\UserPolicies;
use TradeAppOne\Http\Requests\Management\PreferencesFormRequest;

class ManagementNetworkPolicy
{
    protected $request;
    protected $slug;

    public function __construct(PreferencesFormRequest $preferencesFormRequest, string $slug)
    {
        $this->request = $preferencesFormRequest;
        $this->slug    = $slug;
    }

    public function validatePreferences(): ManagementNetworkPolicy
    {
        $businessRules = UserPolicies::setUser($this->request->user());
        $permission    = NetworkPermission::getFullName(PermissionActions::UPDATE_PREFERENCES);

        $businessRules
            ->hasPermission($permission)
            ->hasAuthorizationUnderNetwork($this->slug);

        return $this;
    }

    public function updatePreferences()
    {
        $network                                   = NetworkRepository::findOneBy('slug', $this->slug)->first();
        $preferences                               = $network->preferences ?? [];
        $preferences[$this->request['preference']] = $this->request['value'];

        return $network->update(['preferences' => json_encode($preferences)]);
    }
}
