<?php

namespace Core\HandBooks\Repositories;

use Core\HandBooks\Models\Handbook;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Filters\BaseFilters;
use TradeAppOne\Facades\UserPolicies;

class HandbookFilter extends BaseFilters
{
    protected $builder;

    public function __construct(?Builder $builder = null)
    {
        $this->builder = $builder ?? Handbook::query();
    }

    public function search(string $text): HandbookFilter
    {
        $this->builder->where(function (Builder $query) use ($text) {
            $query->where('title', 'like', "%$text%")
                ->orWhere('description', 'like', "%$text%");
        });

        return $this;
    }

    public function networks(array $networks): HandbookFilter
    {
        $this->builder->whereHas('networks', static function (Builder $query) use ($networks) {
            $query->whereIn('slug', $networks);
        });

        return $this;
    }

    public function roles(array $roles): HandbookFilter
    {
        $this->builder->whereHas('roles', static function (Builder $query) use ($roles) {
            $query->whereIn('slug', $roles);
        });

        return $this;
    }

    public function context(User $user): HandbookFilter
    {
        $authorizations = UserPolicies::setUser($user);

        $this->builder->where(static function (Builder $query) use ($authorizations) {
            $networks = $authorizations->getNetworksAuthorized()->pluck('id');

            $query->where('networksFilterMode', '=', FilterModes::ALL)
                ->orWhere(static function (Builder $query) use ($networks) {
                    $query->whereHas('networks', static function (Builder $builder) use ($networks) {
                        $builder->whereIn('networks.id', $networks);
                    });
                });
        });

        $this->builder->where(static function (Builder $query) use ($authorizations, $user) {

            $roles = $authorizations->getRolesAuthorized()->push($user->role)->pluck('id');

            $query->where('rolesFilterMode', '=', FilterModes::ALL)
                ->orWhere(static function (Builder $query) use ($roles) {
                    $query->whereHas('roles', static function (Builder $builder) use ($roles) {
                        $builder->whereIn('roles.id', $roles);
                    });
                });
        });

        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->builder;
    }
}
