<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\UserPolicies;

class UserRepository extends BaseRepository
{
    protected $model = User::class;
    protected $hierarchyRepository;
    protected $roleRepository;

    public function __construct(
        HierarchyRepository $hierarchyRepository,
        RoleRepository $roleRepository
    ) {
        $this->hierarchyRepository = $hierarchyRepository;
        $this->roleRepository      = $roleRepository;
    }

    public function getAuthenticatedUser(): Authenticatable
    {
        return Auth::user();
    }

    public function filterAndPaginate(array $parameters, int $perPage): LengthAwarePaginator
    {
        return $this->filter($parameters)->paginate($perPage);
    }

    /** @param mixed[] $parameters */
    public function filter(array $parameters): Builder
    {
        $queryWithScope = UserPolicies::getUsersAuthorized();
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'pointsOfSale':
                    if (! isset($value)) {
                        continue;
                    }
                    $queryWithScope = $queryWithScope->whereHas('pointsOfSale', static function ($query) use ($value) {
                        $query->whereIn('pointsOfSale.cnpj', $value);
                    });
                    break;
                case 'roles':
                    $queryWithScope = $queryWithScope->whereHas('role', static function ($query) use ($value) {
                        $query->whereIn('slug', $value);
                    });
                    break;
                case 'networks':
                    $queryWithScope = $queryWithScope->whereHas('role.network', static function ($query) use ($value) {
                        $query->whereIn('slug', $value);
                    });
                    break;
                case 'status':
                    $queryWithScope = $queryWithScope->whereIn('activationStatusCode', $value);
                    break;
                case 'firstName':
                    $queryWithScope = $queryWithScope->
                    where(DB::raw('concat(users.firstName," ",users.lastName)'), 'LIKE', "%{$value}%");
                    break;
                case 'registration':
                    $queryWithScope = $queryWithScope->whereHas('userAuthAlternate', function (Builder $query) use ($value) {
                        $query->where('document', '=', $value);
                    });
                    break;
                default:
                    $queryWithScope = $queryWithScope->where($key, 'like', "%{$value}%");
                    break;
            }
        }
        return $queryWithScope
            ->with('role')
            ->with('pointsOfSale')
            ->with('hierarchies')
            ->with('hierarchies.network')
            ->with('pointsOfSale.network')
            ->with('userAuthAlternate');
    }

    public function findUserByVerificationCode($verificationCode): ?int
    {
        $check = DB::table('userVerifications')
            ->where('verificationCode', $verificationCode)
            ->first();

        return $check === null ? null : $check->userId;
    }

    public function deleteVerificationCode($verificationCode): void
    {
        DB::table('userVerifications')
            ->where('verificationCode', $verificationCode)
            ->delete();
    }

    public function registerVerificationCode($verificationCode, User $user): void
    {
        DB::table('userVerifications')->insert([
            'userId' => $user->id,
            'verificationCode' => $verificationCode,
            'createdAt' => Carbon::now()->toDateTimeString(),
            'updatedAt' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function checkAlreadyHasToken(User $user): bool
    {
        return DB::table('userVerifications')
            ->where('userId', $user->id)
            ->whereDate('createdAt', Carbon::now()->toDateString())
            ->get()->toBase()->isNotEmpty();
    }

    public function findMany(string $with, string $key, array $values): ?Builder
    {
        $userModel = $this->createModel();
        $users     = $userModel->whereHas($with, function ($query) use ($key, $values) {
            $query->whereIn($key, $values);
        });
        return $users;
    }

    public function findOneByWithTrash($key, $value)
    {
        return $this->createModel()->withTrashed()->where($key, '=', $value)->first();
    }

    public function findUsersWithPermission(Builder $users, string $permission): Builder
    {
        $usersWithPermission = $users->whereHas('role.stringPermissions', function ($query) use ($permission) {
            return $query->where('slug', $permission);
        });

        return $usersWithPermission;
    }

    public function findUsersByAPIPermission(string $permission)
    {
        return $this->model::whereHas('role', function ($query) use ($permission) {
            $query->where('slug', $permission);
        });
    }

    public function incrementSigninAttempt(User $user)
    {
        $attempt = $user->signinAttempts;
        $attempt++;
        return $this->update($user, ['signinAttempts' => $attempt]);
    }

    public function resetSigninAttempts(User $user): User
    {
        $this->update($user, ['signinAttempts' => 0]);
        $this->update($user, ['lastSignin' => now()]);

        return $user;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Builder
     */
    public function whereBy(string $key, $value): Builder
    {
        return $this->model::where($key, $value);
    }
}
