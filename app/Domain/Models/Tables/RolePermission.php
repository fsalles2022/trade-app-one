<?php

namespace TradeAppOne\Domain\Models\Tables;

class RolePermission extends BaseModel
{
    protected $table = 'role_permissions';

    protected $fillable = [
        'roleId',
        'permissionsId'
    ];
}
