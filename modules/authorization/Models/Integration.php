<?php

namespace Authorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Operator;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @property integer id
 * @property string accessKey
 * @property integer networkId
 * @property integer operatorId
 * @property integer userId
 * @property string credentialVerifyUrl
 * @property string client
 * @property string subdomain
 */
class Integration extends Model
{
    protected $table      = 'integrations';
    protected $connection = 'outsourced';

    public $timestamps = false;

    protected $fillable = [
        'accessKey',
        'networkId',
        'operatorId',
        'userId',
        'credentialVerifyUrl',
        'subdomain',
        'client'
    ];

    protected $with = [
        'user',
        'whitelist',
        'routes',
        'available_redirects'
    ];

    protected $dbConnection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->dbConnection = App::environment() === Environments::TEST ? 'sqlite': 'mysql';
    }

    public function whitelist(): HasMany
    {
        return $this->hasMany(Whitelist::class, 'integrationId');
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'integrations_routes', 'integrationId', 'routeId');
    }

    public function user(): BelongsTo
    {
        return $this->setConnection($this->dbConnection)->belongsTo(User::class, 'userId');
    }

    public function operator(): BelongsTo
    {
        return $this->setConnection($this->dbConnection)->belongsTo(Operator::class, 'operatorId');
    }

    public function network(): BelongsTo
    {
        return $this->setConnection($this->dbConnection)->belongsTo(Network::class, 'networkId');
    }

    public function available_redirects(): HasMany
    {
        return $this->hasMany(AvailableRedirect::class, 'integrationId');
    }
}
