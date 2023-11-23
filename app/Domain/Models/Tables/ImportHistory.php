<?php

namespace TradeAppOne\Domain\Models\Tables;

/**
 * @property integer id
 * @property string type
 * @property User user
 * @property string inputFile
 * @property string outputFile
 * @property string status
 */
class ImportHistory extends BaseModel
{
    protected $table = 'importHistory';

    protected $fillable = [
        'type',
        'inputFile',
        'outputFile',
        'status'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
