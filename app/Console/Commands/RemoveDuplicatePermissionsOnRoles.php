<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Models\Tables\RolePermission;
use TradeAppOne\Domain\Services\RolePermissionService;

class RemoveDuplicatePermissionsOnRoles extends Command
{
    protected $signature = 'custom:remove-duplicate-permissions';
    protected $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        parent::__construct();
        $this->rolePermissionService = $rolePermissionService;
    }

    public function handle()
    {
        $rolesPermissions    = RolePermission::all();
        $entriesDeleted      = $this->rolePermissionService->removeDuplicate($rolesPermissions);
        $totalEntriesDeleted = count($entriesDeleted);

        $this->info("\nTotal de permissões duplicadas: $totalEntriesDeleted");
    }
}
