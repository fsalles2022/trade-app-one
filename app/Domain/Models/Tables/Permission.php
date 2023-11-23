<?php

namespace TradeAppOne\Domain\Models\Tables;

class Permission extends BaseModel
{
    protected $table = 'permissions';

    protected $fillable = [
        'label',
        'slug',
        'level',
        'client',
    ];

    protected $hidden = [
        'pivot'
    ];

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permissionsId', 'roleId');
    }
}
