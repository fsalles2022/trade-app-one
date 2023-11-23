<?php

namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class AlternateAuthFeatureTest extends TestCase
{
    use UserHelper;

    public const DEFAULT_DOCUMENT = '345331';
    public const DEFAULT_PASSWORD = '91910048';

    /** @test */
    public function post_should_login_with_alternate_document(): void
    {
        $user        = $this->userWithAltenativeAuthAndPermissions()['user'];
        $document    = $user->userAuthAlternate()->first()->document;

        $credentials = ['cpf' => $document, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);
        $response->assertJsonStructure(['data' => ['user', 'token']]);
    }

    /** @test */
    public function post_should_response_with_status_401_when_document_auth_not_vaid(): void
    {
        $credentials = ['cpf' => self::DEFAULT_DOCUMENT, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post_should_response_with_status_401_when_password_is_invalid(): void
    {
        $this->userWithAltenativeAuthAndPermissions()['user'];
        $credentials = ['cpf' => self::DEFAULT_DOCUMENT, 'password' => '000000'];

        $response = $this->post('signin', $credentials);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}