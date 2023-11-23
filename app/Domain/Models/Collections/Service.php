<?php

namespace TradeAppOne\Domain\Models\Collections;

use Core\WebHook\Observers\WebHookServiceObserver;
use Illuminate\Database\Eloquent\Relations\HasOne;
use TradeAppOne\Domain\Components\Traits\ServiceHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

/**
 * @property int id
 * @property Sale sale
 * @property string serviceTransaction
 * @property string operator
 * @property string operation
 * @property string product
 * @property string status
 * @property string statusThirdParty
 * @property string paymentStatus
 * @property string gatewayTransactionId
 * @property array customer
 * @property string mode
 * @property string label
 * @property string price
 * @property string msisdn
 * @property string activationStatusCode
 * @property array operatorIdentifiers
 * @property array log
 * @property string invoiceType
 * @property string sector
 * @Property array recommendation
 * @Property bool hasRecommendation
 * @Property string contractNumber
 * @Property string ibgeCode
 * @Property string installationDate
 * @Property bool remoteSale
 * @property array term
 * @see WebHookServiceObserver
 */
class Service extends BaseModel
{
    use ServiceHelper;

    public const STATUS = [
        ServiceStatus::PENDING_SUBMISSION,
        ServiceStatus::SUBMITTED,
        ServiceStatus::ACCEPTED,
        ServiceStatus::APPROVED,
        ServiceStatus::REJECTED,
        ServiceStatus::CANCELED
    ];

    /** @var string */
    protected $connection = 'mongodb';

    /** @var string[] */
    protected $fillable = [
        'serviceTransaction',
        'sector',
        'operator',
        'operation',
        'mode',
        'status',
        'statusThirdParty',
        'paymentStatus',
        'payment',
        'gatewayTransactionId',
        'product',
        'customer',
        'imei',
        'log',
        'label',
        'price',
        'label',
        'evaluations',
        'evaluationsBonus',
        'operatorIdentifiers',
        'discount',
        'device',
        'recommendation',
        'hasRecommendation',
        'license',
        'nfPrice',
        'invoiceType',
        'times',
        'observations',
        'msisdn',
        'contractNumber',
        'ibgeCode',
        'installationDate',
        'remoteSale',
        'term',
        'areaCode',
        'tradeHub',
        'portedNumberToken',
    ];

    /** @var string[] */
    protected $casts = [
        'hasRecommendation' => 'boolean',
    ];

    public function getImei(): ?string
    {
        return $this->attributes['imei'] ?? null;
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }
}
