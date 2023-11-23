<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\AuthHelper;

class ClaroAutomaticRegistrationCheckStatusFeatureTest extends TestCase
{
    use BindInstance, SivFactoriesHelper, SivBindingHelper, AuthHelper;

    private const URL_AUTOMATIC_REGISTRATION_CHECK_STATUS = '/clarobr/automatic-registration/check-status';

    /** @test */
    public function should_search_registration_created_success(): void
    {
        $this->bindSivResponse();

        $user = UserBuilder::make()
            ->withUserState('user_active')
            ->build();

        $query = http_build_query([
            'protocol' => $user->cpf
        ]);

        $response = $this->authAs($user)
            ->get(self::URL_AUTOMATIC_REGISTRATION_CHECK_STATUS."?{$query}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                "protocol" => $user->cpf,
                "message" => trans('siv::messages.automaticRegistration.success'),
            ]);
        
        $response = json_decode($response->content(), true);

        $this->assertArrayHasKey('protocol', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('user', $response);

        $userResponse = data_get($response, 'user', []);

        $this->assertArrayHasKey('cpf', $userResponse);
        $this->assertArrayHasKey('nome', $userResponse);
        $this->assertArrayHasKey('funcao', $userResponse);
        $this->assertArrayHasKey('rede', $userResponse);
        $this->assertArrayHasKey('canal', $userResponse);
        $this->assertArrayHasKey('funcao_claro', $userResponse);
        $this->assertArrayHasKey('pdv_codigo', $userResponse);
        $this->assertArrayHasKey('data_cadastro', $userResponse);
    }

    /** @test */
    public function should_search_registration_error(): void
    {
        $user = UserBuilder::make()
            ->withUserState('user_active')
            ->build();

        $query = http_build_query([
            'protocol' => "12345678910"
        ]);

        $this->authAs($user)
            ->get(self::URL_AUTOMATIC_REGISTRATION_CHECK_STATUS."?{$query}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                "message" => trans('siv::messages.automaticRegistration.notFound'),
                "protocol" => "12345678910",
            ]);
    }
}
