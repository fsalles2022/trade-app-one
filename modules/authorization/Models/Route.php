<?php

namespace Authorization\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string uri
 * @property string method
 */
class Route extends Model
{
    protected $connection = 'outsourced';

    protected $fillable = [
        'uri',
        'method',
    ];
}
