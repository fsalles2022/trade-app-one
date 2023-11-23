<?php


namespace Outsourced\Partner\Services\Clients;

use Authorization\Models\AvailableRedirect;
use Outsourced\Partner\Helpers\Traits\AuthenticateUrlTrait;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use Authorization\Models\Integration;
use Illuminate\Http\Response;
use Outsourced\Partner\Connections\PartnerHttpClient;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use Outsourced\Partner\Services\Interfaces\PartnerAuthenticationInterface;
use Tradehub\Connection\TradeHubConnection;

class InovaPartnerAuthentication implements PartnerAuthenticationInterface
{
    use AuthenticateUrlTrait;

    private $token;
    private $url;
    private $httpClient;
    private $authorizationType = 'signature';
    private $header;
    private $extraData;

    /** @var TradeHubConnection */
    private $tradeHubConnection;

    public function __construct(Integration $partner, string $token, PartnerHttpClient $httpClient, TradeHubConnection $tradeHubConnection)
    {
        $this->token                = $token;
        $this->url                  = $partner->credentialVerifyUrl;
        $this->httpClient           = $httpClient;
        $this->tradeHubConnection   = $tradeHubConnection;
        $this->setupHeader();
    }

    public function retrieveUserIdentificationDocument(): void
    {
        $this->httpClient->pushHeader($this->header);
        $response = $this->httpClient->get($this->url);
        if ($response->getStatus() !== Response::HTTP_OK) {
            throw PartnerExceptions::errorWhenGetPartnerIdentification($response->get(), $response->getStatus());
        }
        $this->extraData = $response->get('cpf');
    }

    private function setupHeader(): void
    {
        $this->header = [
            'Authorization' => $this->authorizationType . ' ' . $this->token
        ];
    }

    public function getSignInUrl($md5Key, $subdomain = null): string
    {
        return $this->mountSignInUrl($md5Key, $subdomain);
    }

    public function getAvailableRedirectUrl(): ?AvailableRedirect
    {
        return null;
    }

    public function getUserFromDocument(): ?User
    {
        $user = User::where('cpf', $this->extraData)->first();
        if ($user) {
            return $user;
        }
        throw UserExceptions::userNotFound();
    }
}
