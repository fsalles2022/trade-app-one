<?php

namespace Buyback\Helpers;

use Buyback\Exceptions\DeviceNotAcceptedException;

class SumQuestionsWeight
{
    public static function getWeightSum($questions, $questionsAnswer)
    {
        $weightSum = 0;
        foreach ($questions as $question) {
            foreach ($questionsAnswer as $questionAnswer) {
                $questionId = data_get($question, 'id');
                $answerId   = data_get($questionAnswer, 'id');
                if ($questionId == $answerId) {
                    $answer          = data_get($questionAnswer, 'answer');
                    $questionBlocker = data_get($question, 'blocker');
                    if ($answer) {
                        $weightSum += $question['weight'];
                        break;
                    } elseif ($questionBlocker) {
                        throw new DeviceNotAcceptedException();
                    }
                }
            }
        }
        return $weightSum;
    }
}
