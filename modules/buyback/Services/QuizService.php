<?php

namespace Buyback\Services;

use Buyback\Models\Quiz;
use Buyback\Repositories\QuestionRepository;
use Buyback\Repositories\QuizRepository;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\UserPolicies;

class QuizService
{
    protected $quizRepository;
    protected $questionRepository;

    public function __construct(QuizRepository $quizRepository, QuestionRepository $questionRepository)
    {
        $this->quizRepository     = $quizRepository;
        $this->questionRepository = $questionRepository;
    }

    public function quizById(?int $quizId)
    {
        return QuizRepository::find($quizId);
    }

    public function listPaginated(User $user, array $filters = [])
    {
        $networksContext = UserPolicies::setUser($user)->getNetworksAuthorized($user)->pluck('id');

        $quizzes = QuizRepository::quizzesWithQuestions($networksContext, $filters)
            ->paginate(10);

        $quizzes->getCollection()->map(function (Quiz $quiz) {
            $quiz->sumOfWeigths = $quiz->questions->sum('weight');
            $quiz->network      = $quiz->questions->first()->network()->get()->first();
            $quiz->makeVisible(['createdAt']);
        });

        return $quizzes;
    }

    public function create(int $network, array $questions): Quiz
    {
        $quiz = QuizRepository::create();
        $this->createManyQuestions($quiz, $questions, $network);

        return $quiz;
    }

    public function createManyQuestions(Quiz $quiz, array $questions, int $networkId)
    {
        foreach ($questions as $question) {
            $newQuestion = array_merge($question, ['networkId' => $networkId]);

            if (empty(data_get($question, 'weight'))) {
                $newQuestion['weight'] = 0;
            }

            $model = $this->questionRepository->create($newQuestion);

            $quiz->questions()->attach($model);
        }
    }

    public function update(int $id, array $questions): Quiz
    {
        $quiz         = QuizRepository::find($id);
        $network      = $quiz->questions->first()->networkId;
        $questionsOld = $quiz->questions->pluck('id')->toArray();

        $this->questionRepository->delete($questionsOld);
        $this->createManyQuestions($quiz, $questions, $network);

        return $quiz;
    }
}
