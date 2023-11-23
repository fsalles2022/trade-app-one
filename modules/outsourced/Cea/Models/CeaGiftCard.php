<?php

namespace Outsourced\Cea\Models;

use TradeAppOne\Domain\Models\Tables\BaseModel;

class CeaGiftCard extends BaseModel
{
    protected $table      = 'cea_gift_cards';
    protected $connection = 'outsourced';
    
    protected $fillable = [
        'code',
        'value',
        'partner',
        'outsourcedId',
        'reference'
    ];
}
