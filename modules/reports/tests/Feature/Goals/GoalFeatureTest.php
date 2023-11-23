<?php

namespace Reports\Tests\Feature\Goals;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Reports\Tests\Fixture\SalesByPointsOfSalesAndMonthFixture;
use Reports\Goals\Enum\GoalsTypesEnum;
use Reports\Goals\Models\Goal;
use Reports\Goals\Models\GoalType;
use Reports\Tests\Helpers\BindInstance;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\GoalPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\GoalBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class GoalFeatureTest extends TestCase
{
    use BindInstance, AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/goals/import/month';

    /** @test */
    public function get_should_response_with_status_422_when_file_not_sent()
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_403_when_user_not_has_permission()
    {
        $userHelper = (new UserBuilder())->build();

        $csv = UploadedFile::fake()->create('teste.csv', 12);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint, ['file' => $csv]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_response_with_status_200_when_file_sent()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => GoalPermission::getFullName(PermissionActions::IMPORT)
        ]);

        $userHelper = (new UserBuilder())->withPermissions([$permission])->build();

        $importable = \Mockery::mock(ImportEngine::class)->makePartial();
        $importable->shouldReceive('process')->withAnyArgs()->once()->andReturn(new Goal());

        $this->app->singleton(ImportEngine::class, function () use ($importable) {
            return $importable;
        });
        $csv = UploadedFile::fake()->create('teste.csv', 12);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint, ['file' => $csv]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_status_422_when_file_sent_is_not_csv()
    {
        $userHelper = (new UserBuilder())->build();

        $importable = \Mockery::mock(ImportEngine::class)->makePartial();
        $importable->shouldReceive('process')->withAnyArgs()->never()->andReturn(new Goal());

        $this->app->singleton(ImportEngine::class, function () use ($importable) {
            return $importable;
        });
        $csv = UploadedFile::fake()->create('teste.jpeg', 12);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint, ['file' => $csv]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_import_csv()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => GoalPermission::getFullName(PermissionActions::IMPORT)
        ]);

        $type    = factory(GoalType::class)->create();
        $network = factory(Network::class)->create(['slug' => 'tradeup-group']);
        $network->goalsTypes()->attach($type);

        $userHelper  = (new UserBuilder())->withNetwork($network)->withPermissions([$permission])->build();
        $pointOfSale = $userHelper->pointsOfSale->first();

        $rand      = rand(1, 9);
        $stringCsv = "cnpj;ano;mes;".$type->type."\n".
            $pointOfSale->cnpj.";".
            now()->year.";".
            now()->month.";".
            $rand;

        Storage::fake('files');
        Storage::disk('files')->put('goals.csv', $stringCsv);

        $path = Storage::disk('files')->path('goals.csv');
        $csv  = (new UploadedFile($path, 'goals.csv', null, null, null, true));

        $tt = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint, ['file' => $csv]);

        $this->assertDatabaseHas('goals', [
            'month' => now()->month,
            'year' => now()->year,
            'goal' => $rand,
            'goalTypeId' => $type->id,
            'pointOfSaleId' => $pointOfSale->id
        ]);
    }

    /** @test */
    public function post_should_return_csv_based_on_user_auth()
    {
        $permission = factory(Permission::class)->create([
            'slug' => GoalPermission::getFullName(GoalPermission::EXPORT)
        ]);

        $user = (new UserBuilder())->withPermissions([$permission])->build();
        $user->pointsOfSale->first()->update(['label' => 'TAO', 'tradingName' => 'Rede']);

        $goal = (new GoalBuilder())
            ->withTypeString(GoalsTypesEnum::TOTAL)
            ->withMonth(now()->month)
            ->withPointOfSale($user->pointsOfSale->first())
            ->build();

        $data = [
            'pdv1'   => $user->pointsOfSale->first()->cnpj,
            'month1' => now()->format('F-Y')
            ];

        $elasticFixture = (new SalesByPointsOfSalesAndMonthFixture($data))->getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post('goals/export/month');

        $contentResponse  = $response->content();
        $expectedResponse = "Ano;Mes;Rede;Loja;CNPJ;Tipo;Meta;Realizado\n".
            $goal->year.";".
            $goal->month.";".
            $user->pointsOfSale->first()->tradingName.";".
            $user->pointsOfSale->first()->label.";".
            $user->pointsOfSale->first()->cnpj.";TOTAL;".
            $goal->goal.
            ";68\n";

        $this->assertEquals($expectedResponse, $contentResponse);
    }

    /** @test */
    public function post_should_return_csv_based_in_filters()
    {
        $permission = factory(Permission::class)->create([
            'slug' => GoalPermission::getFullName(GoalPermission::EXPORT)
        ]);

        $user = (new UserBuilder())->withPermissions([$permission])->build();
        $user->pointsOfSale->first()->update(['label' => 'TAO', 'tradingName' => 'Rede1']);

        $otherPdv = (new PointOfSaleBuilder())->withUser($user)->build();
        $otherPdv->update(['label' => 'TUO', 'tradingName' => 'Rede2']);

        $type = factory(GoalType::class)->create(['type' => GoalsTypesEnum::TOTAL]);

        $goal = (new GoalBuilder())
            ->withType($type)
            ->withMonth(6)
            ->withPointOfSale($user->pointsOfSale->first())
            ->build();

        $otherGoal = (new GoalBuilder())
            ->withType($type)
            ->withPointOfSale($otherPdv)
            ->withMonth(3)
            ->build();

        $data = [
            'pdv1'   => $user->pointsOfSale->first()->cnpj,
            'month1' => Carbon::create(now()->year, $otherGoal->month, 1)->format('F-Y'),
            'pdv2'   => $otherPdv->cnpj,
            'month2'  => Carbon::create(now()->year, $goal->month, 1)->format('F-Y')
        ];

        $payload = [
            'months'       => [3, 6],
            'pointsOfSale' => [$user->pointsOfSale->first()->cnpj, $otherPdv->cnpj]
        ];

        $elasticFixture = (new SalesByPointsOfSalesAndMonthFixture($data))->getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post('goals/export/month', $payload);

        $contentResponse = $response->content();

        $expectedResponse = "Ano;Mes;Rede;Loja;CNPJ;Tipo;Meta;Realizado\n".
            $goal->year.";".
            $goal->month.";".
            $user->pointsOfSale->first()->tradingName.";".
            $user->pointsOfSale->first()->label.";".
            $user->pointsOfSale->first()->cnpj.";TOTAL;".
            $goal->goal.
            ";69\n".
            $otherGoal->year.";".
            $otherGoal->month.";".
            $otherPdv->tradingName.";".
            $otherPdv->label.";".
            $otherPdv->cnpj.";TOTAL;".
            $otherGoal->goal.
            ";44\n";

        $this->assertEquals($expectedResponse, $contentResponse);
    }

    /** @test */
    public function post_should_return_status_401_when_user_not_permission_export_goals()
    {
        $user = (new UserBuilder())->build();

        factory(Goal::class)->create([
            'pointOfSaleId' => $user->pointsOfSale->first()->id
        ]);

        $data = [
            'pdv1'   => $user->pointsOfSale->first()->cnpj,
            'month1' => now()->format('F-Y')
        ];

        $elasticFixture = (new SalesByPointsOfSalesAndMonthFixture($data))->getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post('goals/export/month');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_return_status_200_when_request_example()
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get('goals/import/month');

        $response->assertStatus(Response::HTTP_OK);
    }
    
    /** @test */
    public function return_goals_with_goalType()
    {
        $role = (new RoleBuilder())->build();
        $user = (new UserBuilder())->withRole($role)->build();

        $goalType     = factory(GoalType::class)->create();
        $pointsOfSale = (new PointOfSaleBuilder())->withUser($user)->build();

        factory(Goal::class)->create([
            'pointOfSaleId' => $pointsOfSale->id,
            'goalTypeId' => $goalType->id
        ]);

        $response = $this->authAs($user)
            ->get('/goals/');
            $response
            ->assertJsonCount(1, 'data')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'year',
                        'month',
                        'goal',
                        'pointOfSale',
                        'goalType'
                    ]
                ]
            ]);
    }
}
