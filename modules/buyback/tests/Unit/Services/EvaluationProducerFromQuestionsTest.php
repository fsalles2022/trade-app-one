<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Helpers\MergeQuestionsWithAnswers;
use Buyback\Services\EvaluationProducerFromQuestions;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use TradeAppOne\Tests\TestCase;

class EvaluationProducerFromQuestionsTest extends TestCase
{
    /** @test */
    public function should_return_from_mount_array(): void
    {
        $question = (new QuestionBuilder())->build()->toArray();
        unset($question['network']);
        $questionAnswer           = $question;
        $questionAnswer['answer'] = 1;

        $result = (new EvaluationProducerFromQuestions(
            ['devicePrice' => '5', 'deviceNote' => '23'],
            MergeQuestionsWithAnswers::merge([$question], [$questionAnswer])
        ))->toArray();

        $this->assertInternalType('array', $result);
        $this->assertEquals($result['price'], 5);
        $this->assertEquals($result['deviceNote'], 23);
        $this->assertEquals($result['questions'][0], $questionAnswer);
    }
}
