<?php


namespace Outsourced\Partner\Helpers\Builders;

use Authorization\Models\Integration;
use Outsourced\Partner\Connections\PartnerHttpClient;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use Outsourced\Partner\Services\Clients\InovaPartnerAuthentication;
use Outsourced\Partner\Services\Clients\SivPartnerAuthentication;
use Outsourced\Partner\Services\Clients\ViaVarejoPartnerAuthentication;
use Outsourced\Partner\Services\Interfaces\PartnerAuthenticationInterface;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use Tradehub\Connection\TradeHubConnection;

class PartnerAuthenticationBuilder
{
    private $partner;
    private $token;
    private $integrations = [
        NetworkEnum::INOVA => InovaPartnerAuthentication::class,
        NetworkEnum::SIV => SivPartnerAuthentication::class,
        NetworkEnum::VIA_VAREJO => ViaVarejoPartnerAuthentication::class
    ];

    public static function create() : PartnerAuthenticationBuilder
    {
        return new self();
    }

    public function forPartner(Integration $partner) : PartnerAuthenticationBuilder
    {
        $this->partner = $partner;
        return $this;
    }

    public function andToken(string $token) : PartnerAuthenticationBuilder
    {
        $this->token = $token;
        return $this;
    }

    public function build() : ?PartnerAuthenticationInterface
    {
        if (! isset($this->integrations[$this->partner->client])) {
            throw PartnerExceptions::partnerNotImplemented();
        }
        return new $this->integrations[$this->partner->client]($this->partner, $this->token, resolve(PartnerHttpClient::class), resolve(TradeHubConnection::class));
    }
}
