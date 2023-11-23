<?php


namespace TradeAppOne\Tests\Feature\Network;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Permissions\NetworkPermission;
use TradeAppOne\Exceptions\SystemExceptions\NetworkExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\ChannelBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class NetworkCreateFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function get_should_return_201_when_create_network_and_hierarchy(): void
    {
        $permission = NetworkPermission::getFullName(NetworkPermission::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $line = $this->payload();
        ChannelBuilder::make()->withChannelName(Channels::MASTER_DEALER)->build();

        $this->authAs($user)
            ->post('networks/', $line)
            ->assertStatus(Response::HTTP_CREATED);

        $hierarchySlug = 'rede-' . $line['slug'];

        $this->assertDatabaseHas('networks', ['cnpj' => $line['cnpj']]);
        $this->assertDatabaseHas('hierarchies', ['slug' => $hierarchySlug]);
    }

    /** @test */
    public function should_return_201_when_create_network_with_role_default(): void
    {
        $permission = NetworkPermission::getFullName(NetworkPermission::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $line = $this->payload();
        ChannelBuilder::make()->withChannelName(Channels::MASTER_DEALER)->build();

        $this->authAs($user)
            ->post('networks/', $line)
            ->assertStatus(Response::HTTP_CREATED);

        $roleSlug = 'diretor-' . $line['slug'];

        $this->assertDatabaseHas('roles', ['slug' => $roleSlug]);
    }

    /** @test */
    public function should_response_403_when_user_has_not_permission(): void
    {
        $user = (new UserBuilder())->build();
        $line = $this->payload();

        $this->authAs($user)
            ->post('networks/', $line)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function should_return_exception_when_has_no_service(): void
    {
        $permission = NetworkPermission::getFullName(NetworkPermission::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $line       = $this->payload();

        $line['availableServices']['LINE_ACTIVATION']['OI'] = [
            0 => 'OI_CONTROLE_NOT_FOUND',
            1 => 'OI_CARTAO_NOT_FOUND'
        ];

        $response = $this->authAs($user)
            ->post('networks/', $line)
            ->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJsonFragment(['shortMessage' => NetworkExceptions::AVAILABLE_SERVICE_NOT_FOUND]);
    }

    /** @test */
    public function should_return_exception_when_has_no_channel(): void
    {
        $permission = NetworkPermission::getFullName(NetworkPermission::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $line       = $this->payload();

        $line['channel'] = 'CHANNEL_NOT_FOUND';

        $this->authAs($user)
            ->post('networks/', $line)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function payload(): array
    {
        return array (
            'slug' => 'test-rede',
            'label' => 'teste',
            'cnpj' => '81176815000128',
            'tradingName' => 'Teste',
            'companyName' => 'Teste Company',
            'zipCode' => '06516080',
            'local' => 'Teste',
            'neighborhood' => 'Teste',
            'state' => 'SP',
            'city' => 'Barueri',
            'number' => '520',
            'channel' => 'MASTER_DEALER',
            'availableServices' =>
                array (
                    'LINE_ACTIVATION' =>
                        array (
                            'OI' =>
                                array (
                                    0 => 'OI_CONTROLE_BOLETO',
                                    1 => 'OI_CONTROLE_CARTAO',
                                ),
                            'TIM' =>
                                array (
                                    0 => 'TIM_CONTROLE_FATURA',
                                    1 => 'TIM_EXPRESS',
                                    2 => 'TIM_PRE_PAGO',
                                ),
                            'VIVO' =>
                                array (
                                    0 => 'CONTROLE_CARTAO',
                                    1 => 'CONTROLE',
                                    2 => 'VIVO_PRE',
                                ),
                            'CLARO' =>
                                array (
                                    0 => 'CONTROLE_BOLETO',
                                    1 => 'CONTROLE_FACIL',
                                    2 => 'CLARO_PRE',
                                ),
                            'NEXTEL' =>
                                array (
                                    0 => 'NEXTEL_CONTROLE_CARTAO',
                                    1 => 'NEXTEL_CONTROLE_BOLETO',
                                ),
                        ),
                ),
        );
    }
}
