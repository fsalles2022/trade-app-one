<?php


namespace Buyback\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property integer id
 * @property Evaluation evaluation
 * @property string sponsor
 * @property double goodValue
 * @property double averageValue
 * @property double defectValue
 */
class EvaluationsBonus extends BaseModel
{
    protected $table = 'evaluations_bonus';

    protected $fillable = [
        'evaluationId',
        'goodValue',
        'averageValue',
        'defectValue',
        'sponsor'
    ];

    protected $hidden = [
        'updatedAt',
        'createdAt',
        'deletedAt'
    ];

    public function rules(): array
    {
        return [
            'evaluationId'  => 'required|numeric',
            'goodValue'     => 'required|numeric',
            'averageValue'  => 'required|numeric',
            'defectValue'   => 'required|numeric',
            'sponsor'       => 'required|string'
        ];
    }

    public function toMongoAggregation(): array
    {
        return [
            'id'            => $this->id,
            'evaluationId'  => $this->evaluation->id,
            'sponsor'       => $this->sponsor,
            'goodValue'     => $this->goodValue,
            'averageValue'  => $this->averageValue,
            'defectValue'   => $this->defectValue,
            'bonusValue'    => $this->bonusValue
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class, 'evaluationId');
    }
}
