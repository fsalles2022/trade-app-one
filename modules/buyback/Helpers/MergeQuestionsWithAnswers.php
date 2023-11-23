<?php

namespace Buyback\Helpers;

use Illuminate\Support\Arr;

class MergeQuestionsWithAnswers
{
    public static function merge(array $questions, array $answers): array
    {
        foreach ($questions as $questionIndex => $question) {
            foreach ($answers as $answerIndex => $questionAnswer) {
                if (Arr::get($question, 'id') === Arr::get($questionAnswer, 'id')) {
                    // TODO, array_merge in a loop is not a best practices, php version >= 7.4 use the Spread Operator
                    $questions[$questionIndex] += $questionAnswer;
                    break;
                }
            }
        }

        return $questions;
    }
}
