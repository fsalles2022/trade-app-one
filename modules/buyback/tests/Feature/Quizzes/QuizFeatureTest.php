<?php

namespace Buyback\Tests\Feature\Quizzes;

use Buyback\Enumerators\QuizPermissions;
use Buyback\Models\Question;
use Buyback\Models\Quiz;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use Illuminate\Http\Response;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class QuizFeatureTest extends TestCase
{
    use AuthHelper;

    const QUIZ = '/buyback/quiz/';

    /** @test */
    public function get_should_return_quizzes_with_questions_paginated()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::VIEW);
        $network    = (new NetworkBuilder())->build();
        $user       = (new UserBuilder())->withNetwork($network)->withPermission($permission)->build();

        $quiz      = factory(Quiz::class)->create();
        $questions = factory(Question::class, 5)->create([
            'networkId' => $network->id
        ]);

        foreach ($questions as $question) {
            $question->quizzes()->attach($quiz);
        }

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::QUIZ);

        $sumOfWeigths = $questions->sum('weight');

        $this->assertCount(5, $response->json('data.0.questions'));
        $this->assertEquals($sumOfWeigths, $response->json('data.0.sumOfWeigths'));
        $this->assertEquals(1, $response->json('total'));
        $this->assertArrayHasKey('network', $response->json('data.0'));
    }

    /** @test */
    public function get_should_status_401_when_user_not_permission_list_quiz()
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::QUIZ);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_return_by_context_network_questions()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::VIEW);

        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->withPermission($permission)->build();

        (new QuestionBuilder())->withNetwork($network)->build();
        (new QuestionBuilder())->build();


        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::QUIZ);

        $this->assertCount(1, $response->json('data.0.questions'));
    }

    /** @test */
    public function get_should_return_filtered_by_network_questions()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::VIEW);

        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->withPermission($permission)->build();
        (new HierarchyBuilder())->withNetwork($network)->withUser($user)->build();

        $network2 = (new NetworkBuilder())->build();

        $quiz = factory(Quiz::class)->create();
        (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        (new QuestionBuilder())->withNetwork($network2)->build();


        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::QUIZ . '?network='. $network->slug);

        $this->assertCount(1, $response->json('data'));
        $this->assertCount(2, $response->json('data.0.questions'));
    }

    /** @test */
    public function get_should_return_quiz_when_has_authorization()
    {
        $user = (new UserBuilder())->build();

        $quiz = (new QuestionBuilder())->withNetwork($user->getNetwork())->build();

        $response = $this
            ->authAs($user)
            ->get(self::QUIZ . $quiz->id);

        $response->assertJsonStructure(['id', 'questions' => ['*' => ['id', 'question', 'weight', 'order', 'blocker', 'description']]]);
        $response->assertJsonFragment(['id' => $quiz->id]);
    }

    /** @test */
    public function get_should_return_exception_when_has_not_authorization_under_quiz_show()
    {
        $user = (new UserBuilder())->build();
        $quiz = (new QuestionBuilder())->build();

        $response = $this
            ->authAs($user)
            ->get(self::QUIZ . $quiz->id);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_QUIZ)]);
    }
}
