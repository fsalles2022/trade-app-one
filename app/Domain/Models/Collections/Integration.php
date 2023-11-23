<?php

namespace TradeAppOne\Domain\Models\Collections;

use TradeAppOne\Domain\Models\Tables\BaseModel;

class Integration extends BaseModel
{
    protected $fillable = ['operator', 'credentials'];
}
