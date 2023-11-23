<?php

use TradeAppOne\Domain\Repositories\Collections\PermissionRepository;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\RoleService;

trait ThirdPartyAccess
{
    public function run()
    {
        $network   = Network::firstOrNew(['slug' => self::NETWORK]);
        $hierarchy = Hierarchy::firstOrNew(['slug' => self::NETWORK]);
        $role      = Role::firstOrNew(['slug' => self::ROLE_SLUG]);
        $user      = User::firstOrNew(['cpf' => self::USER_CPF]);

        $role->fill([
            "name"        => self::ROLE_NAME,
            "level"       => "1",
            "parent"      => self::roleParent()
        ]);

        $role->network()->associate($network)->save();

        $user->fill([
            'firstName'            => self::FIRST_NAME,
            'lastName'             => self::LAST_NAME,
            'cpf'                  => self::USER_CPF,
            'email'                => self::EMAIL,
            "areaCode"             => "11",
            "activationStatusCode" => self::STATUS_CODE,
            "password"             => bcrypt(microtime()),
        ]);

        $this->permissions()->role()->attach($role);

        $user->role()->associate($role)->save();

        if (!$user->hierarchies()->first()) {
            $user->hierarchies()->attach($hierarchy);
        }
    }

    private static function roleParent()
    {
        $roleService = resolve(RoleService::class);
        return $roleService->findOneBySlug(self::ROLE_PARENT)->id;
    }

    private function permissions()
    {
        $permission = resolve(PermissionRepository::class);
        return $permission->findOneBySlug(SalePermission::getFullName(SalePermission::CONTEXT_HIERARCHY));
    }

}