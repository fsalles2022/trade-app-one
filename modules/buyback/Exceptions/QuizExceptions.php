<?php

namespace Buyback\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class QuizExceptions
{
    public const NETWORK_ALREADY_QUIZ = 'networkAlreadyHasQuiz';
    public const NOT_FOUND            = 'quizNotFound';

    public static function networkAlreadyHasQuiz(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NETWORK_ALREADY_QUIZ,
            'message' => trans('buyback::exceptions.' . self::NETWORK_ALREADY_QUIZ),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function notFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('buyback::exceptions.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }
}
