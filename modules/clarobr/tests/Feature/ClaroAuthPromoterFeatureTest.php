<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Connection\SivRoutes;
use ClaroBR\Exceptions\SivAuthExceptions;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class ClaroAuthPromoterFeatureTest extends TestCase
{
    use AuthHelper, SivFactoriesHelper, SivBindingHelper;

    /** @test */
    public function should_return_200_and_user_auth(): void
    {
        $this->bindSivResponse();
        $user    = (new UserBuilder())->build();
        $network = (new NetworkBuilder())->build();
        factory(User::class)->create(['cpf' => '06033171543']);
        factory(PointOfSale::class)->create([
            'providerIdentifiers' => json_encode(['CLARO' => 'OB3W']),
            'networkId' => $network->id
        ]);

        $payload = [
            'username'   => 'S12VC',
            'password'   => 'Cl@ro2019',
            'msisdn'     => '11978456612',
            'cpf'        => '06033171543',
            'token'      => '458055'
        ];

        $this->authAs($user)
            ->post('auth/promoter', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['message' => trans('messages.token_created')]);
    }

    /** @test */
    public function should_return_412_when_promoter_no_auth(): void
    {
        $this->bindSivResponse();
        $user = (new UserBuilder())->build();

        $payload = [
            'username'   => 'SIX12',
            'password'   => 'Cl@ro2019',
            'msisdn'     => '11978456612'
        ];

        $this->authAs($user)
            ->post('auth/promoter', $payload)
            ->assertStatus(Response::HTTP_PRECONDITION_FAILED)
            ->assertJsonFragment(['message' => trans('siv::exceptions.' . SivAuthExceptions::SEND_PROMOTER_TOKEN)]);
    }

    /** @test */
    public function should_return_422_when_authorization_code_is_invalid(): void
    {
        $this->bindSivResponse();
        $user = (new UserBuilder())->build();

        $payload = [
            'username'   => 'TKNF1',
            'password'   => 'Cl@ro2019',
            'msisdn'     => '11985475516',
            'token'      => '996016'
        ];

        $this->authAs($user)
            ->post('auth/promoter', $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment(['message' => 'Código de autorização [11985475516] já validado']);
    }

    /** @test */
    public function should_return_404_when_cpf_has_no_registered(): void
    {
        $this->bindSivResponse();
        $user    = (new UserBuilder())->build();
        $payload = [
            'username'   => 'CPFN5',
            'password'   => 'Cl@ro2019',
            'msisdn'     => '11978456612',
            'cpf'        => '64864898715',
            'token'      => '458055'
        ];

        $this->authAs($user)
            ->post('auth/promoter', $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonFragment(['message' => trans('siv::exceptions.' . SivAuthExceptions::SIV_USER_NOT_FOUND)]);
    }

    /** @test */
    public function should_return_409_when_PDV_has_not_found(): void
    {
        $this->bindSivResponse();
        $user = (new UserBuilder())->build();
        factory(User::class)->create(['cpf' => '06033171543']);

        $payload = [
            'username'   => 'SZJQ8',
            'password'   => 'Cl@ro2019',
            'msisdn'     => '11991266715',
            'cpf'        => '06033171543',
            'token'      => '458055',
            'codigo_pdv' => '21ZY'
        ];

        $this->authAs($user)
            ->post('auth/promoter', $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJsonFragment(['message' => trans('siv::exceptions.' . SivAuthExceptions::SIV_POINT_OF_SALE_NOT_FOUND)]);
    }

    /** @test */
    public function should_return_206_to_choose_a_pdv(): void
    {
        $this->bindSivResponse();
        $user = (new UserBuilder())->build();
        factory(User::class)->create(['cpf' => '06033171543']);

        $payload = [
            'username'   => 'ZALG1',
            'password'   => 'Cl@ro2019',
            'msisdn'     => '11991266715',
            'cpf'        => '06033171543',
            'token'      => '984456'
        ];

        $this->authAs($user)
            ->post('auth/promoter', $payload)
            ->assertStatus(Response::HTTP_PARTIAL_CONTENT)
            ->assertJsonFragment(['message' => trans('siv::exceptions.' . SivAuthExceptions::SELECT_PDV)])
            ->assertJsonStructure([
                'transportedData' => [
                    '0' => [
                        'id',
                        'rede_id',
                        'ddd',
                        'nome',
                        'codigo'
                    ]
                ]
            ]);
    }
}
