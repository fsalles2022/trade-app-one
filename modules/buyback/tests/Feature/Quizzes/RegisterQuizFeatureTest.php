<?php

namespace Buyback\Tests\Feature\Quizzes;

use Buyback\Enumerators\QuizPermissions;
use Buyback\Exceptions\QuizExceptions;
use Buyback\Repositories\QuizRepository;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RegisterQuizFeatureTest extends TestCase
{
    use AuthHelper;

    const ROUTE = 'buyback/quiz';

    /** @test */
    public function post_should_return_200_when_create_quiz()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $network    = $user->getNetwork();
        $payload    = $this->payload($network->slug);

        $response = $this->authAs($user)->post(self::ROUTE, $payload);
        $quiz     = QuizRepository::getQuizzesByNetwork($network->id);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, $quiz->first()->questions);
        $this->assertDatabaseHas('questions', $payload['questions'][0]);
    }

    /** @test */
    public function post_should_return_exception_when_network_already_has_quiz()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $network    = $user->getNetwork()->slug;
        $payload    = $this->payload($network);

        $this->authAs($user)->post(self::ROUTE, $payload);

        $response = $this->authAs($user)->post(self::ROUTE, $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => trans('buyback::exceptions.' . QuizExceptions::NETWORK_ALREADY_QUIZ)]);
    }

    /** @test */
    public function post_should_return_exception_when_user_has_not_permission()
    {
        $user    = (new UserBuilder())->build();
        $network = $user->getNetwork()->slug;
        $payload = $this->payload($network);

        $response = $this->authAs($user)->post(self::ROUTE, $payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
    }

    /** @test */
    public function post_should_return_exception_when_user_has_not_authorization_under_network()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $network    = factory(Network::class)->create();
        $payload    = $this->payload($network->slug);

        $response = $this->authAs($user)->post(self::ROUTE, $payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_NETWORK)]);
    }

    private function payload(string $network)
    {
        return [
            'network' => $network,
            'questions' => [
                [
                    'question' => 'question',
                    'weight' => 12,
                    'order' => 1,
                    'blocker' => 0,
                    'description' => 'description',
                ],
            ],
        ];
    }
}
