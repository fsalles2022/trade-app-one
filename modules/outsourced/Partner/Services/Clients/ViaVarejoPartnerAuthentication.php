<?php


namespace Outsourced\Partner\Services\Clients;

use Authorization\Models\AvailableRedirect;
use Authorization\Models\Integration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Outsourced\Partner\Connections\PartnerHttpClient;
use Outsourced\Partner\Connections\ViaVarejoHeaders;
use Outsourced\Partner\Connections\ViaVarejoRoutes;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use Outsourced\Partner\Helpers\Traits\AuthenticateUrlTrait;
use Outsourced\Partner\Services\Interfaces\PartnerAuthenticationInterface;
use Outsourced\ViaVarejo\Helpers\UserCacheHelper;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use Tradehub\Adapters\TradeHubGetSellerTokenPartner;
use Tradehub\Connection\TradeHubConnection;

class ViaVarejoPartnerAuthentication implements PartnerAuthenticationInterface
{
    use AuthenticateUrlTrait;

    private $token;
    private $url;
    private $httpClient;
    private $authorizationType = 'bearer';
    private $header;
    private $extraData;

    /** @var TradeHubConnection */
    private $tradeHubConnection;

    private const ORIGIN                     = 'parceiro';
    private const DEFAULT_USER_AGENT         = 'tradeup-useragent';
    private const ROUTE_KEYS                 = [1 => 'CASAS_BAHIA', 2 => 'PONTOFRIO'];
    private const ALTERNATE_SUBDOMAIN        = [1 => 'casasbahia', 2 => 'pontofrio'];
    public const VIA_VAREJO_ACCESS_TOKEN_KEY = 'VIA_VAREJO_ACCESS_TOKEN_KEY';

    public function __construct(Integration $partner, string $token, PartnerHttpClient $httpClient, TradeHubConnection $tradeHubConnection)
    {
        $this->token                = $token;
        $this->url                  = $partner->credentialVerifyUrl;
        $this->httpClient           = $httpClient;
        $this->tradeHubConnection   = $tradeHubConnection;

        $this->authenticate();
        $this->setupHeader();
    }

    public function retrieveUserIdentificationDocument(): void
    {
        $response = $this->httpClient->post($this->url, [
            'token' => $this->authorizationType . ' ' . $this->token
        ], $this->header);
        if ($response->getStatus() !== Response::HTTP_OK) {
            throw PartnerExceptions::errorWhenGetPartnerIdentification($response->get(), $response->getStatus());
        }
        $this->extraData = $response->toArray();

        UserCacheHelper::make()->putViaVarejoUserPointOfSaleAlternate(
            $this->getUserFromDocument(),
            (string) data_get($this->extraData, 'filial')
        );
    }

    private function authenticate(): void
    {
        $headers                      = resolve(ViaVarejoHeaders::class);
        $headersKeys                  = $headers->getHeaders();
        $headersKeys['Authorization'] = 'Basic ' . $headers->getKey();
        $headersKeys['Origin']        = self::ORIGIN;
        $headersKeys['User-Agent']    = self::DEFAULT_USER_AGENT;

        $response = $this->httpClient->postFormParams($headers->getUri() . ViaVarejoRoutes::AUTHENTICATE, [
            'scope' => $headers->getScope(),
            'grant_type' => $headers->getGrantType(),
            'canalVenda' => $headers->getCanalVenda(),
            'username' => $headers->getUsername(),
            'password' => $headers->getPassword(),
        ], $headersKeys);

        throw_if(
            $response->getStatus() !== Response::HTTP_OK,
            PartnerExceptions::errorWhenGetPartnerIdentification($response->get(), $response->getStatus())
        );

        $accessToken = $response->get('access_token');
        if ($accessToken !== null) {
            Cache::put(self::VIA_VAREJO_ACCESS_TOKEN_KEY, $accessToken, 60);
        }
    }

    private function setupHeader(): void
    {
        $accessToken  = Cache::get(self::VIA_VAREJO_ACCESS_TOKEN_KEY);
        $this->header = [
            'Authorization' => $this->authorizationType . ' ' . $accessToken,
            'User-Agent' => self::DEFAULT_USER_AGENT,
            'Origin' => self::ORIGIN
        ];
    }

    public function getSignInUrl($md5Key, $subdomain = null): string
    {
        $tokenSeller = $this->getSellerTokenFromViaVarejo();
        $brand           = data_get($this->extraData, 'bandeira');
        $customSubdomain = self::ALTERNATE_SUBDOMAIN[$brand];
        if (app()->environment() !== 'production') {
            $url_array           = explode('.', parse_url(request()->url(), PHP_URL_HOST));
            $subdomain           = data_get($url_array, '0', null);
            $availableSubdomains = [
                'api-tester1' => 'tester1',
                'api-tester2' => 'tester2',
                'api-beta' => 'beta',
                'api-custom' => 'custom'
            ];
            if (array_key_exists($subdomain, $availableSubdomains)) {
                $customSubdomain .= '-' . $availableSubdomains[$subdomain];
            }
        }
        return $this->mountSignInUrl($md5Key, $customSubdomain, $tokenSeller);
    }

    /**
     * @throws \Throwable
     */
    private function getSellerTokenFromViaVarejo(): ?string
    {
        $user = $this->getUserFromDocument();

        // Alternative solution
        $response = $this->tradeHubConnection->authenticateSellerByViaVarejo(
            $this->tradeHubConnection->getToken(),
            $user->cpf
        )->toArray();

        return $response['response']['saleOfServicesAuth']['token'] ?? null;

        // Ideal solution
        $response = $this->tradeHubConnection->getSellerTokenFromViaVarejo(new TradeHubGetSellerTokenPartner($this->token))->toArray();

        return $response['response']['saleOfServicesAuth']['token'] ?? null;
    }

    public function getAvailableRedirectUrl(): ?AvailableRedirect
    {
        $brand        = data_get($this->extraData, 'bandeira');
        $routeByBrand = self::ROUTE_KEYS[$brand];
        return AvailableRedirect::where('routeKey', $routeByBrand)
            ->first();
    }

    public function getUserFromDocument(): ?User
    {
        $identificationDocument = data_get($this->extraData, 'matricula');
        $userAlternate          = UserAuthAlternates::where('document', $identificationDocument)->first();
        $user                   = User::find(data_get($userAlternate, 'userId', 0));
        if ($userAlternate && $user) {
            return $user;
        }
        throw UserExceptions::userNotFound();
    }
}
