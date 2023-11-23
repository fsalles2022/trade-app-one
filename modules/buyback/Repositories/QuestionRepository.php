<?php

namespace Buyback\Repositories;

use Buyback\Models\Question;

class QuestionRepository
{
    public function create(array $attributes): Question
    {
        return Question::create($attributes);
    }

    public function delete(array $ids): bool
    {
        return Question::whereIn('id', $ids)->delete();
    }
}
