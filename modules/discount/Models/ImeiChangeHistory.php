<?php

declare(strict_types=1);

namespace Discount\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @property string serviceTransaction
 * @property string oldImei
 * @property string newImei
 * @property string userIdWhoChanged
 * @property string userCpfWhoChanged
 * @property string userIdWhoAuthorized
 * @property string userCpfWhoAuthorized
 * @property string exchangeDate
 * @property string protocol
 */
class ImeiChangeHistory extends BaseModel
{
    /** @var string */
    protected $table = 'imeiChangeHistory';

    /** @var string[] */
    protected $fillable = [
        'serviceTransaction',
        'oldImei',
        'newImei',
        'userIdWhoChanged',
        'userCpfWhoChanged',
        'userIdWhoAuthorized',
        'userCpfWhoAuthorized',
        'exchangeDate',
        'protocol'
    ];

    public function userModified(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userIdWhoChanged');
    }

    public function userAuthorized(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userIdWhoAuthorized');
    }
}
