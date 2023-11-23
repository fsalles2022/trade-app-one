<?php

namespace TimBR\Connection\Authentication;

use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use TimBR\Connection\Headers\TimHeadersFactory;
use TimBR\Connection\TimBR;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Connection\TimBRRoutes;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Exceptions\NetworkIdentifierNotFound;
use TimBR\Exceptions\TimBRAuthenticationFailed;
use TimBR\Exceptions\TimBREncriptCPFException;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Services\NetworkService;

class AuthenticationConnection
{
    protected const GRANT         = 'authorization_code';
    protected const GRANT_TYPE    = 'grant_type';
    protected const RESPONSE_TYPE = 'code';

    /** @var TimBRUserBearerHttp */
    protected $bearerAssistance;

    /** @var NetworkService */
    protected $networkService;

    public function __construct(TimBRUserBearerHttp $userBearer, NetworkService $networkService)
    {
        $this->bearerAssistance = $userBearer;
        $this->networkService = $networkService;
    }

    public function getBearerToken(string $network, string $cpf): string
    {
        $bearer = Cache::get(TimBRCacheables::USER_BEARER . $network . $cpf);

        if ($bearer === null) {
            $bearer = $this->requestUserBearer($network, $cpf, false);
        }
        
        return $bearer;
    }

    public function getClient($network, $cpf): TimBRHttpClient
    {
        $bearer = $this->getBearerToken($network, $cpf);
        $client = new Client([
            'verify'   => false,
            'base_uri' => TimBR::getWSO2Uri(),
            'headers'  => ['Authorization' => 'Bearer ' . $bearer]
        ]);
        return new TimBRHttpClient($client);
    }

    public function getClientForOrder($network, $cpf): TimBRHttpClient
    {
        $bearer = $this->getBearerToken($network, $cpf);

        $client = new Client([
            'verify'   => false,
            'base_uri' => TimBR::getOrderUri(),
            'headers'  => ['Authorization' => 'Bearer ' . $bearer]
        ]);

        return new TimBRHttpClient($client);
    }

    public function getPMIDClient($network, $cpf): TimBRHttpClient
    {
        $bearer = $this->getBearerToken($network, $cpf);

        $client = new Client([
            'verify'   => false,
            'base_uri' => TimBR::getPMIDUri(),
            'headers'  => ['AuthorizationOAM' => 'Bearer ' . $bearer]
        ]);

        return new TimBRHttpClient($client);
    }

    public function transactionToken(string $network, string $cpf): string
    {
        $client   = $this->getClient($network, $cpf);
        $response = $client->get(TimBRRoutes::TRANSACTION_TOKEN);
        return data_get($response->toArray(), 'transactionToken');
    }

    private function requestUserBearer(string $network, string $cpf, $salesScope = false): string
    {
        try {
            $configs = TimHeadersFactory::make($network);
        } catch (ErrorException $exception) {
            throw new NetworkIdentifierNotFound($exception->getMessage());
        }
        $redirectUri                   = $configs->getRedirectUri();
        $basic                         = $configs->getBasicAuth();
        $encryptedCpf                  = $this->encryptCpf($network, $cpf);
        list($bearerCode, $expiration) = $this->bearerAssistance->requestBearer(
            $network,
            $encryptedCpf,
            $redirectUri,
            $basic
        );

        $cachePrefix = $salesScope ? TimBRCacheables::USER_BEARER_SALE : TimBRCacheables::USER_BEARER;

        Cache::put($cachePrefix . $network . $cpf, $bearerCode, $expiration / 60);
        
        return $bearerCode;
    }

    public function encryptCpf(string $connection, string $cpf): string
    {
        $client       = $this->authenticateNetwork($connection);
        $response     = $client->get(TimBRRoutes::ENCRYPT_CPF . $cpf)->toArray();
        $encriptedCpf = data_get($response, 'encrypted');
        throw_if(empty($encriptedCpf), new TimBREncriptCPFException());
        return $encriptedCpf;
    }

    public function authenticateNetwork(string $network): TimBRHttpClient
    {
        $bearer          = Cache::get(TimBRCacheables::NETWORK_BEARER . $network);
        $configs         = TimHeadersFactory::make($network);
        $authCredentials = $configs->credentials();
        if ($bearer === null) {
            list($bearer, $expiration) = $this->requestNetworkBearer(
                $this->networkService->findOneBySlug($network),
                $authCredentials
            );
            Cache::remember(TimBRCacheables::NETWORK_BEARER . $network, $expiration, function () use ($bearer) {
                return 'Bearer ' . $bearer;
            });
        }
        $client = new Client([
            'verify'   => false,
            'base_uri' => TimBR::getWSO2Uri(),
            'headers'  => ['Authorization' => Cache::get(TimBRCacheables::NETWORK_BEARER . $network)]
        ]);
        return new TimBRHttpClient($client);
    }

    private function requestNetworkBearer(Network $network, array $credentials): array
    {
        $authClient = new Client([
            'verify'   => false,
            'base_uri' => TimBR::getOAMUri(),
            'auth'     => $credentials
        ]);

        $request = [
            'form_params' => [
                'grant_type' => TimBR::GRANT_TYPE,
                'scope'      => TimBR::getAuthScopesByNetwork($network)
            ]
        ];
        $start   = microtime(true);
        try {
            $response = $authClient->post(AuthenticationRoutes::FIRST_STEP, $request)->getBody()->__toString();
            $response = json_decode($response, true);
            return [$response['access_token'], (int) $response['expires_in'] / 60];
        } catch (ClientException $exception) {
            $response = json_encode($exception->getResponse()->getBody()->__toString());
            heimdallLog()
                ->start($start)
                ->end(microtime(true))
                ->realm(Operations::TIM)
                ->request($request)
                ->httpClient($authClient)
                ->url(AuthenticationRoutes::FIRST_STEP)
                ->response($exception->getResponse()->getBody()->__toString())
                ->fire();
            throw new TimBRAuthenticationFailed(data_get($response, 'error_description', ''));
        }
    }
}
