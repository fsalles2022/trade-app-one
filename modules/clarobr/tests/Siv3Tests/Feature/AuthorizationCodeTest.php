<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Feature;

use ClaroBR\Exceptions\Siv3Exceptions;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class AuthorizationCodeTest extends TestCase
{
    use AuthHelper;

    /** @var string */
    private $routeSendAuthorization = 'clarobr/v3/send-authorization';

    /** @var string */
    private $routeCheckAuthorization = 'clarobr/v3/check-authorization';

    /** @var User */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = UserBuilder::make()->build();
    }

    public function test_should_return_success_true_when_to_send_data(): void
    {
        $this->authAs($this->user)->post(
            $this->routeSendAuthorization,
            [
                'customer' => [
                    'phoneNumber' => '11999999999',
                ],
                'type' => 'sms',
                'origin' => 'CONTROLE_BOLETO'
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true]);
    }

    public function test_should_return_message_exception_when_server_error_siv3(): void
    {
        $this->authAs($this->user)->post(
            $this->routeSendAuthorization,
            [
                'customer' => [
                    'phoneNumber' => '11123456789',
                ],
                'type' => 'sms',
                'origin' => 'CONTROLE_BOLETO'
            ]
        )
            ->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
            ->assertJson(Siv3Exceptions::unavailableService()->getError());
    }

    public function test_should_throw_exception_operation_unauthorized(): void
    {
        $this->authAs($this->user)->post(
            $this->routeSendAuthorization,
            [
                'customer' => [
                    'phoneNumber' => '11999999999',
                ],
                'type' => 'sms',
                'origin' => 'CONTROLE_FACIL'
            ]
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(Siv3Exceptions::unauthorizedOperation()->getError());
    }

    public function test_should_success_true_when_to_check(): void
    {
        $this->authAs($this->user)->post(
            $this->routeCheckAuthorization,
            [
                'phoneNumber' => '11999999999',
                'code' => 'abc'
            ]
        )
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['success' => true]);
    }

    public function test_should_exception_message_when_to_send_code_wrong(): void
    {
        $this->authAs($this->user)->post(
            $this->routeCheckAuthorization,
            [
                'phoneNumber' => '11999999999',
                'code' => '2143324'
            ]
        )
           ->assertStatus(Response::HTTP_BAD_REQUEST)
           ->assertJson(Siv3Exceptions::invalidCode()->getError());
    }

    public function test_should_return_exception_message_when_server_error_siv3(): void
    {
         $this->authAs($this->user)->post(
             $this->routeCheckAuthorization,
             [
                'phoneNumber' => '11999999999',
                'code' => 'zxc'
             ]
         )
            ->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
            ->assertJson(Siv3Exceptions::unavailableService()->getError());
    }
}
