<?php

namespace TradeAppOne\Domain\Models\Tables;

use Bulletin\Models\Bulletin;
use Core\Charts\Model\ChartRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use TradeAppOne\Domain\Components\Permissions\PermissionsWrapper;
use TradeAppOne\Domain\Models\Collections\Portfolio\MongoAggregation;

/**
 * @property string name
 * @property string slug
 * @property Network network
 * @property string networkId
 * @property Permission stringPermissions
 */

class Role extends BaseModel implements MongoAggregation
{
    public const ADMIN_TAO = 'administrator-tradeup-group';

    
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'slug',
        'level',
        'permissions',
        'dashboardPermissions',
        'networkId',
        'parent',
        'sequence'
    ];

    protected $hidden = [
        'stringPermissions',
        'createdAt',
        'updatedAt'
    ];

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'roleId', '');
    }

    public function chartRole(): void
    {
        $this->belongsTo(ChartRole::class, 'roleId');
    }

    public function network(): BelongsTo
    {
        // TODO: Um mecanismo para impedir que seja usado diretamente esta instrução.
//        if($this->networkId === null)
//            throw UserExceptions::userHasNoNetwork();

        return $this->belongsTo(Network::class, 'networkId');
    }

    /** @return mixed[] */
    public function getPermissionsAttribute(): array
    {
        $permissions = $this->stringPermissions;
        return PermissionsWrapper::wrap($permissions);
    }

    public function stringPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'roleId', 'permissionsId');
    }

    public function bulletins(): BelongsToMany
    {
        return $this->belongsToMany(Bulletin::class, 'bulletins_roles', 'roleId', 'bulletinId');
    }

    public function getDashboardPermissionsAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function hasAuthorityUnderUser(User $user): bool
    {
        return $this->level < $user->role->level;
    }

    public function hasAuthorityUnderRole(Role $role): bool
    {
        return $this->level < $role->level;
    }

    public function parentIsNull(): bool
    {
        return $this->parent === null;
    }

    public function parentInstance(): BelongsTo
    {
        return $this->belongsTo($this, 'parent');
    }
    
    public function toMongoAggregation(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'network' => $this->network->toMongoAggregation()
        ];
    }
}
