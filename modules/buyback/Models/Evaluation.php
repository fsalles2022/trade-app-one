<?php

namespace Buyback\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property integer id
 * @property Quiz quiz
 * @property string goodValue
 * @property string averageValue
 * @property string defectValue
 */
class Evaluation extends BaseModel
{
    protected $table = 'evaluations';

    protected $fillable = [
        'quizId',
        'deviceNetworkId',
        'goodValue',
        'averageValue',
        'defectValue'
    ];

    protected $hidden = [
        'updatedAt',
        'createdAt',
        'deletedAt'
    ];

    public function rules(): array
    {
        return [
            'quizId'          => 'required|numeric',
            'deviceNetworkId' => 'required|numeric',
            'goodValue'       => 'required|numeric',
            'averageValue'    => 'required|numeric',
            'defectValue'     => 'required|numeric',
        ];
    }

    public function toMongoAggregation(): array
    {
        return [
            "id" => $this->id,
            "goodValue" => $this->goodValue,
            "averageValue" => $this->averageValue,
            "defectValue" => $this->defectValue,
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quizId');
    }

    public function devicesNetwork(): BelongsTo
    {
        return $this->belongsTo(DevicesNetwork::class, 'deviceNetworkId');
    }

    public function evaluationsBonus(): HasMany
    {
        return $this->hasMany(EvaluationsBonus::class);
    }
}
