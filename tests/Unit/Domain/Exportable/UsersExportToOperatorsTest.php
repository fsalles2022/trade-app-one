<?php
namespace TradeAppOne\Tests\Unit\Domain\Exportable;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exports\UsersExportToOperators;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UsersExportToOperatorsTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_a_collection_with_users_and_columns_passed(): void
    {
        $network                = (new NetworkBuilder())->build();
        $user                   = (new UserBuilder())->withNetwork($network)->build();
        $oiUsersWithColumns     = self::oiUsersCollection($network, $user);
        $usersCollection        = $oiUsersWithColumns->values();
        $columns                = array_keys($oiUsersWithColumns->first());
        $usersExportToOperators = new UsersExportToOperators($usersCollection, $columns);
        $this->assertEquals($usersCollection, $usersExportToOperators->collection());
        $this->assertEquals($columns, $usersExportToOperators->headings());
    }

    /** @test */
    public function should_return_create_a_file_when_call_store(): void
    {
        Excel::fake();
        $network            = (new NetworkBuilder())->build();
        $user               = (new UserBuilder())->withNetwork($network)->build();
        $oiUsersWithColumns = self::oiUsersCollection($network, $user);
        $usersCollection        = $oiUsersWithColumns->values();
        $columns                = array_keys($oiUsersWithColumns->first());
        $usersExportToOperators = new UsersExportToOperators($usersCollection, $columns);
        $fileName = 'base-usuarios-' . Carbon::now()->format('d-m-Y') . '.xlsx';
        $usersExportToOperators->store($fileName);
        Excel::assertStored($fileName, function (UsersExportToOperators $export) use ($usersCollection, $columns) {
            return $export->collection() === $usersCollection &&
                $export->headings() === $columns;
        });
    }

    private static function oiUsersCollection(Network $network, User $user): Collection
    {
        $pointOfSale = $network->pointsOfSale()->first();
        return new Collection([[
            'REDE'          => $network->slug,
            'SAP_PDV_OI'    => '1035891',
            'NOME PROMOTOR' => $user->name,
            'CPF'           => $user->cpf,
            'DDD'           => $pointOfSale->areaCode,
            'TELEFONE'      => $pointOfSale->telephone,
            'DT ADMISSÃO'   => '15/05/2018',
            'DT DEMISSÃO'   => '',
            'STATUS'        => 'ATIVO',
            'CANAL'         => 'GRANDE VAREJO',
            'REGIONAL'      => 'NAC',
            'TIPO'          => 'FIXO',
            'RANGE'         => 'VENDEDOR',
        ]]);
    }

    /** @test */
    public function should_export_tim_users_with_network_filter(): void
    {
        $network               = (new NetworkBuilder())->build();
        (new NetworkBuilder())->build();
        $role                  = (new RoleBuilder())->build();
        $user                  = (new UserBuilder())->withNetwork($network)->withRole($role)->build();
        $pointWithoutProviders = (new PointOfSaleBuilder())->withNetwork($network)->build();
        (new UserBuilder())->withNetwork($network)->withRole($role)->withPointOfSale($pointWithoutProviders)->build();

        $pointOfSale1 = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $pointOfSale1->update(['providerIdentifiers' => json_encode(['TIM' => 'CS50_MGCAXI_VA1600_A003'])]);

        $pointOfSale2 = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $pointOfSale2->update(['providerIdentifiers' => json_encode(['TIM' => 'CS50_MGCAXI_VA1600_A004'])]);

        $user->pointsOfSale()->sync([$pointOfSale1->id, $pointOfSale2->id]);

        $response = $this->authAs($user)
            ->post('export/timbr/users', ['networks' => [$network->slug]])
            ->assertStatus(Response::HTTP_OK);

        $zipName = 'tim_users.zip';
        $exists  = Storage::disk('local')->exists($zipName);

        $this->assertEquals(true, $exists);
        $this->assertContains($zipName, $response->headers->get('content-disposition'));

        Storage::disk('local')->delete($zipName);
    }

    /** @test */
    public function should_export_claro_users_with_role_filter(): void
    {
        $network               = (new NetworkBuilder())->build();
        $role                  = (new RoleBuilder())->build();
        $pointWithoutProviders = (new PointOfSaleBuilder())->withNetwork($network)->build();

        $pointOfSale1 = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $pointOfSale1->update(['providerIdentifiers' => json_encode(['CLARO' => 'LX47'])]);

        $pointOfSale2 = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $pointOfSale2->update(['providerIdentifiers' => json_encode(['CLARO' => 'RN18'])]);

        $user  = (new UserBuilder())->withNetwork($network)->withRole($role)->build();
        $user2 = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointWithoutProviders)->build();

        $user->pointsOfSale()->sync([$pointOfSale1->id, $pointOfSale2->id]);

        $response = $this->authAs($user)
            ->post('export/claro/users', ['roles' =>[$role->slug]])
            ->assertStatus(Response::HTTP_OK);

        $this->assertContains($user->cpf, $response->getContent());
        $this->assertNotContains($user2->cpf, $response->content());
    }
}