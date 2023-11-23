<?php

namespace Buyback\Repositories;

use Buyback\Exceptions\QuizExceptions;
use Buyback\Models\Quiz;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class QuizRepository
{
    public static function quizzesWithQuestions($context, array $filters = []): Builder
    {
        return Quiz::whereHas('questions.network', function ($network) use ($context, $filters) {
                $network->whereIn('id', $context)
                    ->where('slug', 'like', data_get($filters, 'network') ?? "%");
        })->with('questions');
    }

    public static function create(): Quiz
    {
        $quiz = new Quiz();
        $quiz->save();

        return $quiz;
    }

    public static function find($id): Quiz
    {
        $quiz = Quiz::find($id);
        throw_if(empty($quiz), QuizExceptions::notFound());

        return $quiz;
    }

    public static function getQuizzesByNetwork($networks): Collection
    {
        return Quiz::whereHas('questions', function (Builder $query) use ($networks) {
            $query->where('networkId', '=', array_wrap($networks));
        })->get();
    }
}
