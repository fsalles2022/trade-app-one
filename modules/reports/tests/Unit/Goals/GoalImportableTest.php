<?php

namespace Reports\Tests\Unit\Goals;

use Illuminate\Support\Facades\Lang;
use Reports\Goals\Importables\GoalImportable;
use Reports\Goals\Models\GoalType;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class GoalImportableTest extends TestCase
{
    /** @test */
    public function should_return_example_with_types_user()
    {
        $user  = (new UserBuilder())->build();
        $types = factory(GoalType::class, 5)->create();

        $user->getNetwork()->goalsTypes()->attach($types);

        $goalImportable = app()->makeWith(
            GoalImportable::class,
            [
                'pointsOfSale' => $user->pointsOfSale,
                'goalsTypes'   => $types
            ]
        );

        $example = $goalImportable->getExample();

        $columnsDefault = $goalImportable->getColumns();

        $columnsExtras   = $types->pluck('type')->toArray();
        $columnsExpected = array_merge($columnsDefault, $columnsExtras);

        $this->assertEquals($columnsExpected, $example[0]);
    }

    /** @test */
    public function should_return_instance_when_type_exists()
    {
        $user = (new UserBuilder())->build();

        $types = factory(GoalType::class, 2)->create();
        $user->getNetwork()->goalsTypes()->attach($types);

        $goalImportable = app()->makeWith(
            GoalImportable::class,
            [
                'goalsTypes' => $types
            ]
        );

        $received = $goalImportable->typeExists($types->first()->type);

        $this->assertInstanceOf(GoalImportable::class, $received);
    }

    /** @test */
    public function should_return_exception_when_type_not_exists()
    {
        $this->expectExceptionMessage(trans('goal::exceptions.goal.invalid_type', ['type' => 'fake']));

        $types = factory(GoalType::class, 2)->create();

        $goalImportable = app()->makeWith(
            GoalImportable::class,
            [
                'goalsTypes' => $types
            ]
        );

        $goalImportable->typeExists('fake');
    }

    /** @test */
    public function should_return_true_when_goal_is_valid()
    {
        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->isNotEmpty(435);

        $this->assertEquals(true, $received);
    }

    /** @test */
    public function should_return_false_when_goal_is_not_valid_pass_0()
    {
        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->isNotEmpty(0);

        $this->assertEquals(false, $received);
    }

    /** @test */
    public function should_return_false_when_goal_is_not_valid_pass_empty()
    {
        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->isNotEmpty('');

        $this->assertEquals(false, $received);
    }

    /** @test */
    public function should_return_exception_when_not_permission_under_cnpj()
    {
        $this->expectExceptionMessage(trans('goal::exceptions.goal.pdv_not_authorized'));

        $pointOfSale = (new PointOfSaleBuilder())->build();

        $goalImportable = app()->makeWith(
            GoalImportable::class,
            [
                'pointsOfSale' => $pointOfSale::all()
            ]
        );

        $goalImportable->hasAuthorizationUnderCnpj(0000000000000);
    }

    /** @test */
    public function should_return_instance_when_not_permission_under_cnpj()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $goalImportable = app()->makeWith(
            GoalImportable::class,
            [
                'pointsOfSale' => $pointOfSale::all()
            ]
        );

        $received = $goalImportable->hasAuthorizationUnderCnpj($pointOfSale->cnpj);

        $this->assertInstanceOf(GoalImportable::class, $received);
    }

    /** @test */
    public function should_return_translation_to_import()
    {
        Lang::shouldReceive('get')->once()->andReturn(['type' => 'tipo']);

        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->translateTypeToImport('tipo');

        $this->assertEquals('type', $received);
    }

    /** @test */
    public function should_return_type_no_translation_to_import()
    {
        Lang::shouldReceive('get')->once()->andReturn(['type' => 'tipo']);

        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->translateTypeToImport('otherType');

        $this->assertEquals('otherType', $received);
    }

    /** @test */
    public function should_return_translation_to_export()
    {
        Lang::shouldReceive('get')->once()->andReturn(['type' => 'tipo']);

        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->translateTypeToExport('type');

        $this->assertEquals('tipo', $received);
    }

    /** @test */
    public function should_return_type_no_translation_to_export()
    {
        Lang::shouldReceive('get')->once()->andReturn(['type' => 'tipo']);

        $goalImportable = resolve(GoalImportable::class);
        $received       = $goalImportable->translateTypeToExport('otherType');

        $this->assertEquals('otherType', $received);
    }

    /** @test */
    public function should_return_instance_when_cnpj_exists()
    {
        $pointOfSale    = (new PointOfSaleBuilder())->build();
        $goalImportable = resolve(GoalImportable::class);

        $received = $goalImportable->cnpjExists($pointOfSale->cnpj);

        $this->assertInstanceOf(GoalImportable::class, $received);
    }

    /** @test */
    public function should_return_exception_when_cnpj_not_exists()
    {
        $goalImportable = resolve(GoalImportable::class);

        $this->expectException(PointOfSaleNotFoundException::class);
        $goalImportable->cnpjExists('1234567891011');
    }
}
