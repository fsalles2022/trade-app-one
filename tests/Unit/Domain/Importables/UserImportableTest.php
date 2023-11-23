<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;

use Illuminate\Support\Facades\Auth;
use stdClass;
use TradeAppOne\Domain\Importables\UserImportable;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\SystemExceptions\DateExceptions;
use TradeAppOne\Facades\SyncUserOperators;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UserImportableTest extends TestCase
{
    /** @test */
    public function should_create_a_new_user_if_does_not_exists(): void
    {
        $helper = $this->helperImportable();

        $userToImport = factory(User::class)->make();

        $userToImport->birthday =  '10/04/2015';

        $line         = $this->getLine($userToImport, $helper->pointOfSale->cnpj, $helper->user->role, $helper->hierarchy->slug);
        $userCreated = $helper->instance->processLine($line);

        $this->assertDatabaseHas('users', [
            'id'        => $userCreated->id,
            'firstName' => $userCreated->firstName,
            'lastName'  => $userCreated->lastName,
            'cpf'       => $userToImport->cpf,
            'email'     => $userToImport->email
        ]);

        $this->assertDatabaseHas('pointsOfSale_users', [
            'pointsOfSaleId' => $userCreated->pointsOfSale->first()->id,
            'userId'         => $userCreated->id
        ]);
    }

    /** @test */
    public function should_update_user_if_exists(): void
    {
        $helper = $this->helperImportable();

        $userToImport = (new UserBuilder())->withPointOfSale($helper->pointOfSale)->build();
        $newPDV       = (new PointOfSaleBuilder())->withUser($helper->user)->withNetwork($helper->network)->build();
        $newRole      = (new RoleBuilder())->withNetwork($helper->network)->build();
        $helper->instance->businessRules->setAuthorizations($helper->user);

        $userToImport->email     = 'jailson@mail.com';
        $userToImport->birthday  = '10/04/2015';

        $line        = $this->getLine($userToImport, $newPDV->cnpj, $newRole, $helper->hierarchy->slug);
        $userCreated = $helper->instance->processLine($line);

        $this->assertDatabaseHas('users', [
            'id'        => $userCreated->id,
            'email'     => $userToImport->email,
            'birthday'  => '2015-04-10',
            'roleId'    => $newRole->id
        ]);

        $this->assertDatabaseHas('pointsOfSale_users', [
            'pointsOfSaleId' => $newPDV->id,
            'userId'         => $userCreated->id
        ]);
    }

    /** @test */
    public function should_return_exception_when_birtday_is_malformed()
    {
        $helper   = $this->helperImportable();

        $userToImport = factory(User::class)->make();

        $line = $this->getLine($userToImport, $helper->pointOfSale->cnpj);

        $this->expectExceptionMessage(trans('exceptions.date.' . DateExceptions::FORMAT_INCORRECT, ['date' => $userToImport->birthday]));

        $helper->instance->processLine($line);
    }

    /** @test */
    public function should_update_deletedAt_when_user_exists()
    {
        $helper = $this->helperImportable();

        $userToImport = (new UserBuilder())->withPointOfSale($helper->pointOfSale)->build();

        $userToImport->birthday  = '10/04/2015';

        $helper->instance->businessRules->setAuthorizations($helper->user);

        $line = $this->getLine($userToImport, $helper->pointOfSale->cnpj, null, $helper->hierarchy->slug);
        $userToImport->delete();

        $userCreated = $helper->instance->processLine($line);

        $this->assertEquals(null, $userCreated->deletedAt);
    }

    private function getLine(User $user = null, string $cnpj = null, Role $role = null, string $hierarchy = null, string $matriculation = null)
    {
        $userImportable = resolve(UserImportable::class);

        $columns = array_keys($userImportable   ->getColumns());
        $lines   = $userImportable->getExample($user, $cnpj, $role, $hierarchy);
        return array_combine($columns, $lines);
    }

    private function helperImportable(): stdClass
    {
        $helper = new stdClass();
        $helper->user        = (new UserBuilder())->build();
        $helper->pointOfSale = $helper->user->pointsOfSale->first();
        $helper->role        = $helper->user->role;
        $helper->network     = $helper->user->getNetwork();
        $helper->hierarchy   = (new HierarchyBuilder())
            ->withNetwork($helper->network)
            ->withUser($helper->user)
            ->build();

        Auth::setUser($helper->user);

        $helper->instance = new UserImportable(
            resolve(UserService::class),
            resolve(BusinessRules::class)->setAuthorizations($helper->user)
        );

        SyncUserOperators::shouldReceive('sync')->atLeast();
        return $helper;
    }
}