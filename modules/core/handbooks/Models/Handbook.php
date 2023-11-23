<?php

namespace Core\HandBooks\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @property int id
 * @property string module
 * @property string title
 * @property Collection roles
 * @property string networksFilterMode
 * @property string rolesFilterMode
 * @property Collection networks
 * @property string category
 * @property string file
 * @property string type
 * @property User user
 */

class Handbook extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'userId',
        'title',
        'description',
        'type',
        'file',
        'module',
        'category',
        'networksFilterMode',
        'rolesFilterMode'
    ];

    protected $hidden = [
      'deletedAt', 'updatedAt'
    ];

    public function networks()
    {
        return $this->belongsToMany(Network::class, 'handbooks_networks', 'handbookId', 'networkId');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'handbooks_roles', 'handbookId', 'roleId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
