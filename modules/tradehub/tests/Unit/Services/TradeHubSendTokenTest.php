<?php

declare(strict_types=1);

namespace Tradehub\Tests\Unit\Services;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Tradehub\Exceptions\TradeHubExceptions;

class TradeHubSendTokenTest extends TestCase
{
    use AuthHelper;

    /** @var string */
    private $routeSendPortabilityToken = 'tradehub/send-portability-token';

    /** @var string */
    private $routeCheckPortabilityToken = 'tradehub/check-portability-token';

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
            $this->routeSendPortabilityToken,
            [
                'customer' => [
                    'phoneNumber' => '11999999999',
                ]
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true]);
    }

    public function test_should_return_message_exception_when_server_error_tradehub(): void
    {
        $this->authAs($this->user)->post(
            $this->routeSendPortabilityToken,
            [
                'customer' => [
                    'phoneNumber' => '11987634239',
                ]
            ]
        )
            ->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
            ->assertJson((new TradeHubExceptions)->unavailableService(null)->getError());
    }

    public function test_should_success_true_when_to_check(): void
    {
        $this->authAs($this->user)->post(
            $this->routeCheckPortabilityToken,
            [
                'phoneNumber' => '11999999999',
                'code' => '1234'
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true]);
    }

    public function test_should_exception_message_when_to_send_code_wrong(): void
    {
        $this->authAs($this->user)->post(
            $this->routeCheckPortabilityToken,
            [
                'phoneNumber' => '11999999999',
                'code' => '1124211'
            ]
        )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(TradeHubExceptions::invalidCode()->getError());
    }

    public function test_should_return_exception_message_when_server_error_tradehub(): void
    {
        $this->authAs($this->user)->post(
            $this->routeCheckPortabilityToken,
            [
                'phoneNumber' => '11999999999',
                'code' => '4321'
            ]
        )
            ->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
            ->assertJson((new TradeHubExceptions)->unavailableService(null)->getError());
    }
}
