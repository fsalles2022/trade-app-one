<?php

namespace Authorization\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string ip
 * @property integer integrationId
 */
class Whitelist extends Model
{
    protected $connection = 'outsourced';

    protected $fillable = [
        'ip',
        'integrationId',
    ];
}
