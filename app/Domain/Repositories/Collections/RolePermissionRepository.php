<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Database\Eloquent\Model;
use TradeAppOne\Domain\Models\Tables\RolePermission;

class RolePermissionRepository
{
    protected $model = RolePermission::class;

    public function delete(Model $instance)
    {
        return $instance->delete();
    }
}
