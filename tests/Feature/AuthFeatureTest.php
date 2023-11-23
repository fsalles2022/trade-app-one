<?php

namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use UserHelper;

    public const DEFAULT_PASSWORD = '91910048';

    /** @test */
    public function post_should_response_with_available_services(): void
    {
        $user        = $this->userWithPermissions()['user'];
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $decryptedUser = json_decode(base64_decode($response->json('data.user')), true);
        $this->assertArrayHasKey('availableServices', $decryptedUser);
    }

    /** @test */
    public function get_should_response_200_with_available_services(): void
    {
        $payload  = $this->userWithPermissions();
        $response = $this
            ->withHeader('Authorization', $payload['token'])
            ->json('GET', '/me');

        $response->assertJsonStructure(['availableServices']);
    }

    /** @test */
    public function post_should_response_with_status_200_when_credentials_are_valid(): void
    {
        $user        = $this->userWithPermissions()['user'];
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_response_with_status_401_when_user_does_not_have_permissions(): void
    {
        $user        = (new UserBuilder())->build();
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post_should_response_with_valid_token_when_credentials_are_valid(): void
    {
        $user        = $this->userWithPermissions()['user'];
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertJsonFragment(['token']);
    }


    /** @test */
    public function post_should_response_with_valid_dashboard_permissions_when_credentials_are_valid(): void
    {
        $user        = $this->userWithPermissions()['user'];
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $decryptedUser = json_decode(base64_decode($response->json('data.user')), true);
        $this->assertArrayHasKey('dashboardPermissions', data_get($decryptedUser, 'role'));
    }

    /** @test */
    public function post_should_response_with_status_401_when_cpf_is_invalid(): void
    {
        $credentials = ['cpf' => '23178564313', 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post_should_response_with_status_401_when_password_is_invalid(): void
    {
        $user        = $this->userWithPermissions()['user'];
        $credentials = ['cpf' => $user->cpf, 'password' => '000000'];

        $response = $this->post('signin', $credentials);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post_should_response_with_status_200_when_attempts_limit_is_reseted(): void
    {
        $user                 = $this->userWithPermissions()['user'];
        $user->signinAttempts = User::ATTEMPTS_LIMIT;
        $user->save();

        $validCredentials   = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];
        $invalidCredentials = ['cpf' => $user->cpf, 'password' => '000000'];

        $this->post('signin', $validCredentials);
        $this->post('signin', $invalidCredentials);

        $response = $this->post('signin', $validCredentials);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_response_with_status_428_when_is_first_access(): void
    {
        $user                       = $this->userWithPermissions()['user'];
        $user->activationStatusCode = UserStatus::NON_VERIFIED;
        $user->save();
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);
        $response->assertStatus(Response::HTTP_PRECONDITION_REQUIRED);
    }

    /** @test */
    public function post_should_response_with_verification_status_code_when_is_first_access(): void
    {
        $user                       = $this->userWithPermissions()['user'];
        $user->activationStatusCode = UserStatus::NON_VERIFIED;
        $user->save();
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertJsonFragment(['verificationCode']);
    }

    /** @test */
    public function post_should_response_with_401_when_user_attempts_is_bigger_than_limit(): void
    {
        $user                 = $this->userWithPermissions()['user'];
        $user->signinAttempts = User::ATTEMPTS_LIMIT + 1;
        $user->save();
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->post('signin', $credentials);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post_should_response_with_status_401_when_credentials_are_invalid(): void
    {
        $credentials = ['cpf' => '74737432440', 'password' => '00000000'];

        $response = $this->json('POST', 'signin', $credentials);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post_should_response_with_a_invalid_crential_error_when_credentials_are_invalid(): void
    {
        $credentials = ['cpf' => '74737432440', 'password' => '00000000'];

        $response = $this->json('POST', 'signin', $credentials);

        $data = ['error' => trans('messages.token_invalid_credentials')];
        $response->assertJsonFragment($data);
    }

    /** @test */
    public function post_with_valid_token_should_response_with_status_200(): void
    {
        $user        = $this->userWithPermissions()['user'];
        $credentials = ['cpf' => $user->cpf, 'password' => self::DEFAULT_PASSWORD];

        $response = $this->json('POST', 'signin', $credentials);
        $token    = $response->decodeResponseJson()['data']['token'];
        $response = $this->json('GET', '/me', ['token' => $token]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_response_status_401_when_invalid_token_sent(): void
    {
        $response = $this->json(
            'GET',
            '/me',
            [
                'Authorization' => 'Bearer syJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHR
                wOi8vdHJhZGUtYXBwLmxvY2FsL2FwaS9hdXRoIiwiaWF0IjoxNTE0NDg2MTkxLC
                JleHAiOjE1MTQ0ODk3OTEsIm5iZiI6MTUxNDQ4NjE5MSwianRpIjoicURURkJaR
                UZJUGRTaXhQVCIsInN1YiI6Im1vbmdvZGIiLCJwcnYiOiIyNDAxNmNlNzRiNGY1
                YjQwN2ZjZmUxM2RiNTZkNzMwMzgxYmNmNDA3In0.GtpCd7gnigPATgRdyLgaiDo
                YJt6YJZerBTKwkHnxJ9A',
            ]
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function post__should_response_with_status_401_when_token_dont_sent(): void
    {
        $response = $this->json('GET', '/me', ['token' => '']);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function get_should_response_status_200_when_user_signout(): void
    {
        $response = $this
            ->withHeader('Authorization', $this->userWithPermissions()['token'])
            ->json('GET', '/signout');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_status_500_when_user_already_signout(): void
    {
        $response = $this
            ->withHeader('Authorization', $this->userWithPermissions()['token'])
            ->json('GET', '/signout');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_200_with_user_authenticated(): void
    {
        $payload  = $this->userWithPermissions();
        $response = $this
            ->withHeader('Authorization', $payload['token'])
            ->json('GET', '/me');

        $response->assertJson(['firstName' => $payload['user']->firstName]);
    }
}
