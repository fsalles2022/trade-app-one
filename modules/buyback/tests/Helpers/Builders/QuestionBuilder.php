<?php

namespace Buyback\Tests\Helpers\Builders;

use Buyback\Models\Question;
use Buyback\Models\Quiz;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;

class QuestionBuilder
{

    protected $network;
    protected $quiz;
    protected $states = [];
    protected $weight;

    public function withNetwork(Network $network): QuestionBuilder
    {
        $this->network = $network;
        return $this;
    }

    public function withQuiz(Quiz $quiz): QuestionBuilder
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function withStates(array $states): QuestionBuilder
    {
        $this->states = $states;
        return $this;
    }

    public function withWeight(int $weight)
    {
        $this->weight = $weight;
        return $this;
    }

    public function generateQuestionTimes(int $quantity)
    {
        $builded = collect();
        foreach (range(1, $quantity) as $index) {
            $builded->push($this->build());
        }
        return $builded;
    }

    public function build(): Question
    {
        $networkEntity   = $this->network ?? (new NetworkBuilder())->build();
        $quizEntity      = $this->quiz ?? factory(Quiz::class)->create();
        $questionFactory = factory(Question::class)->states($this->states);

        $questionEntity = $this->weight
            ? $questionFactory->make(['weight' => $this->weight])
            : $questionFactory->make();

        $questionEntity->network()->associate($networkEntity)->save();
        $questionEntity->quizzes()->attach($quizEntity);

        return $questionEntity;
    }
}
