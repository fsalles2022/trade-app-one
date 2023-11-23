<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use TradeAppOne\Domain\Models\Tables\PasswordReset;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\UserPolicies;

class PasswordResetRepository extends BaseRepository
{
    protected $model = PasswordReset::class;
    protected $hierarchyRepository;

    public function __construct(HierarchyRepository $hierarchyRepository)
    {
        $this->hierarchyRepository = $hierarchyRepository;
    }

    public function compareManagersPassword(Builder $managersList, string $password)
    {
        foreach ($managersList->get() as $manager) {
            if (password_verify($password, $manager->password)) {
                return $manager;
            }
        }

        return false;
    }

    public function deletePasswordResetRequest($passwordReset): void
    {
        $passwordReset->delete();
    }

    public function filter(array $parameters): Builder
    {
        $builder = $this->passwordResetBuilder();

        foreach ($parameters as $key => $value) {
            $functionName = 'filterBy' . Str::ucfirst($key);
            if (isset($value) && method_exists($this, $functionName)) {
                $builder = $this->$functionName($builder, $value);
            }
        }

        return $builder;
    }

    private function passwordResetBuilder()
    {
        $builder         = PasswordReset::with('pointOfSale:id,slug,label', 'user:id,firstName,lastName,cpf');
        $authorizedUsers = UserPolicies::getUsersAuthorized()
            ->whereIn('id', $builder->pluck('userId'))
            ->pluck('id');

        return $builder->whereIn('userId', $authorizedUsers);
    }

    public function filterByCpf(Builder $builder, string $value): Builder
    {
        return $builder->whereHas('user', static function (Builder $userQuery) use ($value) {
            $userQuery->where('cpf', 'like', "$value%");
        });
    }

    public function filterByNetworks(Builder $builder, array $value): Builder
    {
        return $builder->whereHas('pointOfSale', static function (Builder $pointsOfSaleQuery) use ($value) {
            $pointsOfSaleQuery->whereHas('network', static function (Builder $networkQuery) use ($value) {
                $networkQuery->whereIn('slug', $value);
            });
        });
    }

    public function filterByPointsOfSale(Builder $builder, array $value): Builder
    {
        return $builder->whereHas('pointOfSale', static function (Builder $pointOfSaleQuery) use ($value) {
                $pointOfSaleQuery->whereIn('cnpj', $value);
        });
    }

    public function findPasswordResetRequestByUserId(int $userId): ?PasswordReset
    {
        return $this->findOneBy('userId', $userId);
    }

    public function registerPasswordResetRequest(User $user): void
    {
        $pointOfSale = $user->pointsOfSale()->first();

        $this->firstOrCreate([
            'userId'         => $user->id,
            'pointsOfSaleId' => $pointOfSale instanceof PointOfSale ? $pointOfSale->id : null
        ]);
    }

    public function updateManagerId(PasswordReset $passwordReset, int $managerId): bool
    {
        return $passwordReset->update(['managerId' => $managerId]);
    }

    public function updateStatus(PasswordReset $passwordReset, String $status): bool
    {
        return $passwordReset->update(['status' => $status]);
    }
}
