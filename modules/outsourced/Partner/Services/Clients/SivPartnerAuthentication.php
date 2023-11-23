<?php


namespace Outsourced\Partner\Services\Clients;

use Authorization\Models\Integration;
use ClaroBR\Enumerators\ClaroBRCaches;
use ClaroBR\SivHeaders;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Outsourced\Partner\Connections\PartnerHttpClient;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use Outsourced\Partner\Helpers\Traits\AuthenticateUrlTrait;
use Outsourced\Partner\Services\Interfaces\PartnerAuthenticationInterface;
use Authorization\Models\AvailableRedirect;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use Tradehub\Connection\TradeHubConnection;

class SivPartnerAuthentication implements PartnerAuthenticationInterface
{
    use AuthenticateUrlTrait;

    private $token;
    private $url;
    private $authorizationType = 'Bearer';
    private $httpClient;
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

    private function setupHeader(): void
    {
        $sivHeaders   = resolve(SivHeaders::class);
        $customApiKey = $sivHeaders->getCustomHeaders();
        $this->header = [
            'Authorization' => $this->authorizationType . ' ' . $this->token,
            'Accept-Language' => 'application/json',
            'x-api-key' => data_get($customApiKey, 'x-api-key')
        ];
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
            Cache::put(ClaroBRCaches::USER_BEARER . $user->cpf, $this->token, ClaroBRCaches::AUTHENTICATION_DUE);
            return $user;
        }
        throw UserExceptions::userNotFound();
    }
}
