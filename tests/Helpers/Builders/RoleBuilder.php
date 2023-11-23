<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;

class RoleBuilder
{
    private $network;
    private $roleState = 'admin';
    private $roleParent;
    private $removeNetwork = false;

    public static function make(): RoleBuilder
    {
        return new self();
    }

    public function withNetwork(Network $network) :RoleBuilder
    {
        $this->network = $network;
        return $this;
    }

    public function withRoleState(String $roleState) :RoleBuilder
    {
        $this->roleState = $roleState;
        return $this;
    }

    public function withParent(Role $roleParent)
    {
        $this->roleParent = $roleParent;
        return $this;
    }

    public function withoutNetwork() : RoleBuilder
    {
        $this->removeNetwork = true;
        return $this;
    }

    public function build() :Role
    {
        $networkEntity      = $this->removeNetwork ? null : $this->network ?? factory(Network::class)->create();
        $roleFactory        = factory(Role::class)
            ->states($this->roleState)
            ->make(['sequence' => $this->sequenceGenerator(),
                    'parent'   => $this->roleParent
            ]);

        $roleEntity = $this->associateRoleRelations($networkEntity, $roleFactory);
        return $roleEntity;
    }

    private function associateRoleRelations(?Network $networkEntity, Role $roleEntity) : Role
    {
        $roleEntity->network()->associate($networkEntity)->save();
        return $roleEntity;
    }

    private function sequenceGenerator(): string
    {
        $sequence = null;
        if (is_null($this->roleParent)) {
            $sequence = Role::all()->count() + 1;
        } else {
            $sequenceParent = $this->roleParent->sequence;
            $parentId       = $this->roleParent->id;
            $sequenceCount  = Role::where('parent', $parentId)->count() + 1;
            $sequence       = $sequenceParent . '.' . $sequenceCount;
        }

        return $sequence;
    }
}
