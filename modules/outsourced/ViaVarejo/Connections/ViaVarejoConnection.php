<?php


namespace Outsourced\ViaVarejo\Connections;

use Illuminate\Support\Facades\Cache;
use Outsourced\Partner\Connections\ViaVarejoHeaders;
use Outsourced\Partner\Connections\ViaVarejoRoutes as ViaVarejoPartnerRoutes;
use Outsourced\Partner\Services\Clients\ViaVarejoPartnerAuthentication;
use \TradeAppOne\Domain\HttpClients\Responseable;

class ViaVarejoConnection
{
    private $httpClient;
    private const ORIGIN             = 'parceiro';
    private const DEFAULT_USER_AGENT = 'tradeup-useragent';

    public function __construct(ViaVarejoHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function saveSale(array $payload): ?Responseable
    {
        $viaVarejoAccessToken = Cache::get(ViaVarejoPartnerAuthentication::VIA_VAREJO_ACCESS_TOKEN_KEY);

        if ($viaVarejoAccessToken === null) {
            $viaVarejoAccessToken = $this->authenticate();
        }

        if ($viaVarejoAccessToken !== null) {
            $this->httpClient->pushHeader([
                'Authorization' => 'bearer '. $viaVarejoAccessToken,
                'User-Agent'    => self::DEFAULT_USER_AGENT,
                'Origin'        => self::ORIGIN
            ]);
            return $this->httpClient->post(ViaVarejoRoutes::SALE, $payload);
        }
        return null;
    }

    public function checkCpf(string $cpf): Responseable
    {
        $viaVarejoAccessToken = Cache::get(ViaVarejoPartnerAuthentication::VIA_VAREJO_ACCESS_TOKEN_KEY);
        $this->httpClient->pushHeader([
            'Authorization' => 'bearer ' . $viaVarejoAccessToken,
            'User-Agent'    => self::DEFAULT_USER_AGENT,
            'Origin'        => self::ORIGIN
        ]);

        return $this->httpClient->get(ViaVarejoRoutes::VALIDATE . '/' . $cpf);
    }

    private function authenticate(): ?string
    {
        $headers                      = resolve(ViaVarejoHeaders::class);
        $headersKeys                  = $headers->getHeaders();
        $headersKeys['Authorization'] = 'Basic ' . $headers->getKey();
        $headersKeys['Origin']        = self::ORIGIN;
        $headersKeys['User-Agent']    = self::DEFAULT_USER_AGENT;

        $response = $this->httpClient->postFormParams($headers->getUri() . ViaVarejoPartnerRoutes::AUTHENTICATE, [
            'scope' => $headers->getScope(),
            'grant_type' => $headers->getGrantType(),
            'canalVenda' => $headers->getCanalVenda(),
            'username' => $headers->getUsername(),
            'password' => $headers->getPassword(),
        ], $headersKeys);

        return $response->get('access_token');
    }
}
