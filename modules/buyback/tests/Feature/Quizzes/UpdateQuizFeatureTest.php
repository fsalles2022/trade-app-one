<?php

namespace Buyback\Tests\Feature\Quizzes;

use Buyback\Enumerators\QuizPermissions;
use Buyback\Exceptions\QuizExceptions;
use Buyback\Models\Question;
use Buyback\Models\Quiz;
use Illuminate\Http\Response;
use stdClass;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UpdateQuizFeatureTest extends TestCase
{
    use AuthHelper;

    const ROUTE = 'buyback/quiz/';

    /** @test */
    public function put_return_200_when_updated()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::EDIT);

        $helper = $this->helper($permission);

        $response = $this->authAs($helper->user)
            ->put(self::ROUTE.$helper->quiz->id, $helper->payload);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas(
            'questions',
            array_merge($helper->payload['questions'][0], ['networkId' => $helper->network->id])
        );
    }

    /** @test */
    public function put_return_403_when_user_has_not_permission()
    {
        $helper = $this->helper('FAKE-PERMISSION');

        $response = $this->authAs($helper->user)
            ->put(self::ROUTE.$helper->quiz->id, $helper->payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
    }

    /** @test */
    public function put_return_404_when_quiz_not_found()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::EDIT);

        $helper = $this->helper($permission);

        $response = $this->authAs($helper->user)
            ->put(self::ROUTE.'9999', $helper->payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonFragment(['message' => trans('buyback::exceptions.' . QuizExceptions::NOT_FOUND)]);
    }

    /** @test */
    public function put_return_403_when_user_hasNotAuthorizationUnderQuiz()
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::EDIT);
        $helper     = $this->helper($permission);
        $user       = (new UserBuilder())->withPermissions($helper->permission)->build();

        $response = $this->authAs($user)
            ->put(self::ROUTE.$helper->quiz->id, $helper->payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_QUIZ)]);
    }

    private function helper(string $permission)
    {
        $helper = new stdClass();

        $helper->permission = [factory(Permission::class)->create(['slug' => $permission])];

        $helper->user    = (new UserBuilder())->withPermissions($helper->permission)->build();
        $helper->network = $helper->user->getNetwork();

        $helper->quiz = factory(Quiz::class)->create();

        $question = factory(Question::class)->create([
            'networkId' => $helper->network->id
        ]);

        $helper->quiz->questions()->attach($question);

        $helper->payload = [
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

        return $helper;
    }
}
