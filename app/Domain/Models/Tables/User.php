<?php

namespace TradeAppOne\Domain\Models\Tables;

use Bulletin\Models\Bulletin;
use Carbon\Carbon;
use Discount\Models\ImeiChangeHistory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Terms\Models\UserTerm;
use TradeAppOne\Domain\Components\Helpers\ContextHelper;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Collections\Portfolio\MongoAggregation;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property integer id
 * @property string firstName
 * @property string lastName
 * @property string email
 * @property string cpf
 * @property string areaCode
 * @property string activationStatusCode
 * @property Role role
 * @property Collection pointsOfSale
 * @property Carbon createdAt
 * @property Carbon updatedAt
 * @property Carbon deletedAt
 * @property Collection operators
 */
class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject,
    MongoAggregation
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable, SoftDeletes;

    const TRADEUP_GROUP = 'tradeup-group';

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'cpf',
        'birthday',
        'password',
        'areaCode',
        'activationStatusCode',
        'lastSignin',
        'signinAttempts',
        'integrationCredentials',
        'activeToken',
        'createdAt',
        'updatedAt',
    ];

    protected $hidden = [
        'password',
        'integrationCredentials',
        'roleId',
        'pivot'
    ];

    protected $dates = ['lastSignin'];

    public const ATTEMPTS_LIMIT = 15;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function getPassword(): ?string
    {
        return $this->attributes['password'] ?? null;
    }

    public function userAuthAlternate(): HasOne
    {
        return $this->hasOne(UserAuthAlternates::class, 'userId', '');
    }

    public function pointsOfSale(): BelongsToMany
    {
        return $this->belongsToMany(PointOfSale::class, 'pointsOfSale_users', 'userId', 'pointsOfSaleId');
    }

    public function hierarchies(): BelongsToMany
    {
        return $this->belongsToMany(Hierarchy::class, 'hierarchies_users', 'userId', 'hierarchyId');
    }

    public function operators(): BelongsToMany
    {
        return $this->belongsToMany(Operator::class, 'operators_users', 'userId', 'operatorId');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'roleId', 'id');
    }

    public function terms(): HasMany
    {
        return $this->hasMany(UserTerm::class, 'userId', 'id');
    }

    public function changedImeiLogs(): HasMany
    {
        return $this->hasMany(ImeiChangeHistory::class, 'userIdWhoChanged');
    }

    public function authorizedImeiLogs(): HasMany
    {
        return $this->hasMany(ImeiChangeHistory::class, 'userIdWhoAuthorized');
    }

    public function isVerified(): bool
    {
        return $this->activationStatusCode === UserStatus::VERIFIED || $this->isActivated();
    }

    public function isActivated(): bool
    {
        return $this->activationStatusCode === UserStatus::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->activationStatusCode === UserStatus::INACTIVE;
    }

    public function getUserContext(string $module): string
    {
        $permissions = ($this && $this->role) ? $this->role->permissions : null;
        return ContextHelper::getContext($permissions, SubSystemEnum::API, $module);
    }

    public function pendingRegistrations()
    {
        return $this->hasMany(UserPendingRegistration::class, 'userId', 'id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role->stringPermissions->where('slug', $permission)->isNotEmpty();
    }

    public function passwordResets()
    {
        return $this->hasMany(PasswordReset::class, 'userId', 'id')->withTrashed();
    }

    public function hasSomePermission(array $permissions): bool
    {
        return $this->role->stringPermissions->whereIn('slug', $permissions)->isNotEmpty();
    }

    public function bulletins(): BelongsToMany
    {
        return $this->belongsToMany(Bulletin::class, 'bulletins_users', 'userId', 'bulletinId')
            ->as('bulletinsUsers')
            ->withPivot('seen');
    }

    public function toMongoAggregation(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'birthday' => $this->birthday,
            'areaCode' => $this->areaCode,
            'activationStatusCode' => $this->activationStatusCode,
            'lastSignin' => $this->lastSignin,
            'role' => $this->role->toMongoAggregation()
        ];
    }

    public function hasAuthorityUnderNetwork(int $networkId): bool
    {
        $userBelongsToNetwork = ($this->getNetwork()->id === $networkId);

        if ($userBelongsToNetwork) {
            return true;
        }

        $userIsFromTradeUp = $this->getNetwork()->slug === self::TRADEUP_GROUP;

        if ($userIsFromTradeUp) {
            return true;
        }

        return false;
    }

    public function getNetwork(): ?Network
    {
        if ($this->role->networkId !== null) {
            return $this->role->network;
        }

        return $this->pointsOfSale()->first()->network;
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'users_channels', 'userId', 'channelId');
    }

    public function isPromoter($forOptions = false): bool
    {
        if ($forOptions) {
            return $this->role->networkId === null;
        }
        return $this->operators->isNotEmpty();
    }

    public function isInovaPromoter(): bool
    {
        if (! $this->role) {
            return false;
        }
        return str_contains('vendedor-promotor-inova', $this->role->slug);
    }

    public function isDistribuicaoChannel(): bool
    {
        $network = $this->getNetwork();
        return $network->channel === 'DISTRIBUIÇÃO';
    }

    public function isOnChannel(string $channel, bool $mode): bool
    {
        if ($mode) {
            $userChannel = $this->channels->pluck('name')->first();
            return $userChannel === $channel;
        }
        $network        = $this->getNetwork();
        $networkChannel = $network->channels->pluck('name')->first();
        return $networkChannel === $channel;
    }

    public function getOperators(): Collection
    {
        $operators = $this->operators()->get();
        return $operators->isEmpty() ? collect([]) : $operators;
    }
}
