<?php

namespace Buyback\Models;

use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property integer id
 * @property Collection questions
 * @property Evaluation evaluations
 */
class Quiz extends BaseModel
{
    protected $table = 'quizzes';

    protected $hidden = [
        'updatedAt',
        'createdAt',
        'deletedAt'
    ];

    protected $with = [
        'questions'
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'questions_quizzes', 'quizId', 'questionId')->orderBy('order');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'quizId', '');
    }
}
