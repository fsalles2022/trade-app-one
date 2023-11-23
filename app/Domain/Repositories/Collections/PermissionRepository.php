<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use TradeAppOne\Domain\Models\Tables\Permission;

class PermissionRepository
{
    protected $model = Permission::class;

    public function findOneBySlug($value)
    {
        return (new $this->model)->where('slug', '=', $value)->first();
    }
}
