<?php


namespace TradeAppOne\Tests\Unit\Domain\Importables;


use TradeAppOne\Domain\Importables\UserImportableDelete;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UserImportableDeleteTest extends TestCase
{
    /** @test */
    public function should_delete_user_when_exists(): void
    {
        $userDelete = (new UserBuilder())->build();

        $importable = $this->helperImportableDelete();

        $received = $importable->instance->processLine(['cpf' => $userDelete->cpf]);

        $this->assertEquals(true, $received);
    }

    private function helperImportableDelete(): \stdClass
    {
        $helperDelete = new \stdClass();
        $helperDelete->user = (new UserBuilder())->build();

        $helperDelete->instance = new UserImportableDelete(
            resolve(UserService::class),
            resolve(BusinessRules::class)->setAuthorizations($helperDelete->user)
        );

        return $helperDelete;
    }
}