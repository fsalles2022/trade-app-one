<?php

namespace Buyback\Tests\Unit\Console;

use Buyback\Console\WaybillGenerateCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class WaybillGenerateCommandTest extends TestCase
{
    const COMMAND = 'waybill:generate';

    /** @test */
    public function should_command_waybill_generate_exists()
    {
        $this->assertTrue(array_has(Artisan::all(), self::COMMAND));
    }

    /** @test */
    public function shound_create_waybill_without_parameters()
    {
        App::shouldReceive('environment')->andReturn(Environments::TEST);

        (new PointOfSaleBuilder())->build()
            ->update(['slug' => WaybillGenerateCommand::DEFAULT_POINT_OF_SALE]);

        (new UserBuilder())
            ->build()
            ->update(['cpf' => WaybillGenerateCommand::DEFAULT_SALESMAN_CPF]);


        Artisan::call(self::COMMAND);
        $this->assertDatabaseHas(
            'sales',
            [
            'pointOfSale.slug' => WaybillGenerateCommand::DEFAULT_POINT_OF_SALE,
            'user.cpf' => WaybillGenerateCommand::DEFAULT_SALESMAN_CPF,
            'services.operation' => Operations::SALDAO_INFORMATICA],
            'mongodb'
        );
    }

    /** @test */
    public function shound_create_waybill_with_parameters()
    {
        App::shouldReceive('environment')->andReturn(Environments::TEST);

        $pointOfSale = (new PointOfSaleBuilder())->build();
        $user        = (new UserBuilder())->build();

        Artisan::call(self::COMMAND, [
            '--pointOfSale' => $pointOfSale->slug,
            '--operation' => Operations::IPLACE,
            '--user' => $user->cpf
        ]);

        $this->assertDatabaseHas(
            'sales',
            [
            'pointOfSale.slug' => $pointOfSale->slug,
            'services.operation' => Operations::IPLACE],
            'mongodb'
        );
    }

    /** @test */
    public function shound_return_null_when_is_not_test_env()
    {
        (new PointOfSaleBuilder())
            ->build()
            ->update(['slug' => WaybillGenerateCommand::DEFAULT_POINT_OF_SALE]);

        App::shouldReceive('environment')->andReturn(Environments::PRODUCTION);

        $this->expectExceptionMessage('Não é possível executar o comando neste ambiente.');
        Artisan::call(self::COMMAND);
    }

    /** @test */
    public function shound_return_exception_when_cnpj_invalid()
    {
        App::shouldReceive('environment')->andReturn(Environments::TEST);

        $this->expectException(ValidationException::class);
        Artisan::call(self::COMMAND, ['--pointOfSale' => '1234']);
    }

    /** @test */
    public function shound_return_exception_when_operation_invalid()
    {
        App::shouldReceive('environment')->andReturn(Environments::TEST);

        $this->expectException(ValidationException::class);
        Artisan::call(self::COMMAND, ['--operation' => Operations::CLARO_CONTROLE]);
    }

    /** @test */
    public function shound_return_exception_when_salesman_not_found()
    {
        App::shouldReceive('environment')->andReturn(Environments::TEST);

        (new PointOfSaleBuilder())
            ->build()
            ->update(['slug' => WaybillGenerateCommand::DEFAULT_POINT_OF_SALE]);

        $this->expectException(UserNotFoundException::class);
        Artisan::call(self::COMMAND);
    }
}
