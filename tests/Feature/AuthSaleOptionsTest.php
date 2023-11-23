<?php


namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class AuthSaleOptionsTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_sale_options_of_an_authenticated_user(): void
    {
        $permission = SalePermission::getFullName(SalePermission::ASSOCIATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();

        $service = factory(Service::class)->create([
            'sector'    => 'LINE_ACTIVATION',
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_BOLETO'
        ]);

        $serviceOptions = factory(ServiceOption::class)->create([
            'action' => 'CARTEIRIZACAO'
        ]);

        $availableService = factory(AvailableService::class)->create([
            'serviceId' => $service->id,
            'pointOfSaleId' => $user->pointsOfSale()->first()->id,
            'networkId' => null
        ]);

        $availableService->options()->sync($serviceOptions);

        $payload = [
            'sector' => 'LINE_ACTIVATION',
            'operator' => 'CLARO',
            'operation' => 'CONTROLE_BOLETO'
        ];

        $this->authAs($user)
            ->json('GET',  'sales/options',$payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([$serviceOptions->action]);
    }

    /** @test */
    public function should_return_an_empty_sale_options_array_when_user_has_no_permission(): void
    {
        $user    = (new UserBuilder())->build();

        $service = factory(Service::class)->create([
            'sector'    => 'LINE_ACTIVATION',
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_BOLETO'
        ]);

        $serviceOptions = factory(ServiceOption::class)->create([
            'action' => 'CARTEIRIZACAO'
        ]);

        $availableService = factory(AvailableService::class)->create([
            'serviceId' => $service->id,
            'pointOfSaleId' => $user->pointsOfSale()->first()->id,
            'networkId' => $user->getNetwork()->id
        ]);

        $availableService->options()->sync($serviceOptions);

        $payload = [
            'sector' => 'LINE_ACTIVATION',
            'operator' => 'CLARO',
            'operation' => 'CONTROLE_BOLETO'
        ];

        $this->authAs($user)
            ->json('GET',  'sales/options',$payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([]);
    }
}
