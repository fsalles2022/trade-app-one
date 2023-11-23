<?php

namespace Buyback\Models;

use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Network;

/**
 * @property integer id
 * @property string question
 * @property string weight
 * @property string order
 * @property boolean blocker
 */
class Question extends BaseModel
{
    protected $table = 'questions';

    protected $fillable = [
        'question',
        'order',
        'weight',
        'blocker',
        'networkId',
        'description'
    ];

    protected $hidden = [
        'pivot',
        'networkId',
        'updatedAt',
        'createdAt',
        'deletedAt'
    ];

    public function rules(): array
    {
        return [
            'question'    => 'required',
            'order' => 'required|numeric',
            'weight' => 'required|numeric',
            'blocker'  => 'required|boolean',
        ];
    }

    public function network()
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'questions_quizzes', 'questionId', 'quizId');
    }
}
