<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\User;

class UserBuilder
{
    private $network;
    private $pointOfSale;
    private $permissions = [];
    private $permissionSlug;
    private $hierarchy;
    private $roleState = 'admin';
    private $userState = 'user_active';
    private $role;
    private $doNotPopulateServices = false;
    private $withoutRoleNetwork = false;
    private $customParameters;
    private $operators = [];
    private $channel;

    public static function make(): UserBuilder
    {
        return new self();
    }

    public function withNetwork(Network $network): UserBuilder
    {
        $this->network = $network;
        return $this;
    }

    public function withoutRoleNetwork(): UserBuilder
    {
        $this->withoutRoleNetwork = true;
        return $this;
    }

    public function withPointOfSale(PointOfSale $pointOfSale): UserBuilder
    {
        $this->pointOfSale = $pointOfSale;
        return $this;
    }

    public function withCustomParameters(array $parameters): UserBuilder
    {
        $this->customParameters = $parameters;
        return $this;
    }

    public function withRole(Role $role): UserBuilder
    {
        $this->role = $role;
        return $this;
    }

    public function withHierarchy(Hierarchy $hierarchy): UserBuilder
    {
        $this->hierarchy = $hierarchy;
        return $this;
    }

    public function withRoleState(string $roleState): UserBuilder
    {
        $this->roleState = $roleState;
        return $this;
    }

    public function withUserState(string $userState): UserBuilder
    {
        $this->userState = $userState;
        return $this;
    }

    public function withPermissions(array $permissions): UserBuilder
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function withPermission(string $permission): UserBuilder
    {
        $this->permissionSlug = $permission;
        return $this;
    }

    public function withOperators($operators): UserBuilder
    {
        $this->operators = array_wrap($operators);
        return $this;
    }

    public function withUserChannel($channel): UserBuilder
    {
        $this->channel = $channel;
        return $this;
    }

    public function doNotPopulateServices(): UserBuilder
    {
        $this->doNotPopulateServices = true;
        return $this;
    }

    public function generateUserTimes(int $quantity): void
    {
        foreach (range(1, $quantity) as $index) {
            $this->build();
        }
    }

    public function build(): User
    {
        $networkEntity = $this->getTheCorrectNetwork();
        if (!$this->doNotPopulateServices && $networkEntity->services()->count() === 0) {
            $this->createNetworkServices($networkEntity);
        }
        $roleFactory = $this->getTheCorrectRole();
        $pointOfSaleEntity = $this->pointOfSale ?? factory(PointOfSale::class)->make();
        $permissionsArray = $this->createPermissions();

        $userFactory = factory(User::class)->states($this->userState)->make();
        if ($this->customParameters !== null) {
            $userFactory = factory(User::class)->states($this->userState)->make($this->customParameters);
        }

        $userEntity = $this->associateUserRelations($networkEntity, $pointOfSaleEntity,
            $roleFactory, $userFactory, $permissionsArray);

        if ($this->hierarchy) {
            $userEntity->hierarchies()->attach($this->hierarchy);
            $userEntity->pointsOfSale()->attach($this->hierarchy);
        }

        if ($this->channel) {
            $userEntity->channels()->attach($this->channel);
        }

        return $userEntity;
    }

    private function getTheCorrectRole(): Role
    {
        if ($this->role) {
            return $this->role;
        }

        if ($this->network) {
            return (new RoleBuilder())
                ->withNetwork($this->network)
                ->withRoleState($this->roleState)
                ->build();
        }

        return (new RoleBuilder())
            ->withRoleState($this->roleState)
            ->build();
    }

    private function getTheCorrectNetwork(): Network
    {
        if (filled($this->network)) {
            return $this->network;
        } elseif (filled($this->pointOfSale) && filled($this->pointOfSale->network)) {
            return $this->pointOfSale->network;
        } else {
            return factory(Network::class)->create();
        }
    }

    private function associateUserRelations(
        Network $networkEntity,
        PointOfSale $pointOfSaleEntity,
        Role $roleEntity,
        User $userEntity,
        array $permissionsArray
    ): User
    {
        $pointOfSaleEntity->network()->associate($networkEntity)->save();
        $userEntity->role()->associate($roleEntity)->save();
        $userEntity->pointsOfSale()->attach($pointOfSaleEntity);
        if (!$this->withoutRoleNetwork) {
            $roleEntity->network()->associate($networkEntity)->save();
        }

        foreach ($permissionsArray as $permissionFactory) {
            $roleEntity->stringPermissions()->attach($permissionFactory);
        }

        foreach ($this->operators as $operator) {
            $userEntity->operators()->attach($operator);
        }

        return $userEntity;
    }

    private function createPermissions(): array
    {
        $permissions = $this->permissions ?? [factory(Permission::class)->create()];

        if ($this->permissionSlug) {
            $permission = factory(Permission::class)->create([
                'slug' => $this->permissionSlug
            ]);

            $permissions[] = $permission;
        }

        return $permissions;
    }

    private function createNetworkServices($network): void
    {
        $services[] = factory(Service::class)->create(['sector' => 'COURSES', 'operator' => 'UOL', 'operation' => 'UOL_PLUS']);
        $services[] = factory(Service::class)->create(['sector' => 'COURSES', 'operator' => 'UOL', 'operation' => 'UOL_PROFESSIONAL']);
        $services[] = factory(Service::class)->create(['sector' => 'COURSES', 'operator' => 'UOL', 'operation' => 'UOL_STANDARD']);
        $services[] = factory(Service::class)->create(['sector' => 'INSURANCE', 'operator' => 'GENERALI', 'operation' => 'GENERALI_ELECTRONICS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_BANDA_LARGA']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_POS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_PRE']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_PRE_CHIP_COMBO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CONTROLE_BOLETO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CONTROLE_FACIL']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'NEXTEL', 'operation' => 'NEXTEL_CONTROLE_BOLETO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'NEXTEL', 'operation' => 'NEXTEL_CONTROLE_CARTAO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'OI', 'operation' => 'OI_CONTROLE_BOLETO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'OI', 'operation' => 'OI_CONTROLE_CARTAO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'CONTROLE_FATURA']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'EXPRESS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'TIM_CONTROLE_FATURA']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'TIM_EXPRESS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'TIM_PRE_PAGO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'CONTROLE']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'CONTROLE_CARTAO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'VIVO_INTERNET_MOVEL_POS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'VIVO_POS_PAGO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'VIVO_PRE']);
        $services[] = factory(Service::class)->create(['sector' => 'MOBILE_APPS', 'operator' => 'MOVILE', 'operation' => 'MOVILE_CUBES']);
        $services[] = factory(Service::class)->create(['sector' => 'SECURITY_SYSTEM', 'operator' => 'MCAFEE', 'operation' => 'MCAFEE_MULTI_ACCESS']);
        $services[] = factory(Service::class)->create(['sector' => 'SECURITY_SYSTEM', 'operator' => 'MCAFEE', 'operation' => 'MCAFEE_MULTI_ACCESS_TRIAL']);
        $services[] = factory(Service::class)->create(['sector' => 'SECURITY_SYSTEM', 'operator' => 'MCAFEE', 'operation' => 'MOBILE_SECURITY']);
        $services[] = factory(Service::class)->create(['sector' => 'TRADE_IN', 'operator' => 'TRADE_IN_MOBILE', 'operation' => 'BRUSED']);
        $services[] = factory(Service::class)->create(['sector' => 'TRADE_IN', 'operator' => 'TRADE_IN_MOBILE', 'operation' => 'IPLACE']);
        $services[] = factory(Service::class)->create(['sector' => 'TRADE_IN', 'operator' => 'TRADE_IN_MOBILE', 'operation' => 'SALDAO_INFORMATICA']);

        $network->services()->sync(array_pluck($services, 'id', []));
    }
}
