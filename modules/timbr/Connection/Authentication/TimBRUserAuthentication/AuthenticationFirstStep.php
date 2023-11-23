<?php

namespace TimBR\Connection\Authentication\TimBRUserAuthentication;

use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TimBR\Components\TimCookieJar;
use TimBR\Connection\Authentication\AuthenticationRoutes;
use TimBR\Connection\Authentication\NetworkCustomClientsTimEnum;
use TimBR\Connection\Headers\TimHeadersFactory;
use TimBR\Connection\TimBR;
use TimBR\Exceptions\TimBRAuthenticationCookieNotFound;
use TimBR\Exceptions\TimBRAuthenticationInvalidCookies;
use TradeAppOne\Domain\Services\NetworkService;

class AuthenticationFirstStep
{
    protected const MANDATORY_COOKIES = [
        'OAMAuthnHintCookie',
        'OAMRequestContext_',
        'OAM_REQ_0',
        'OAM_REQ_COUNT',
        'JSESSIONID'
    ];
    protected $redirectHistory        = [];

    /** @var CookieJarInterface */
    protected $cookies;
    protected $authNCookie;
    protected $arrayCookies = [];

    /** @var TimBRClientUserAuthentication */
    protected $client;

    /** @var NetworkService */
    protected $networkService;

    public function __construct(TimBRClientUserAuthentication $client, NetworkService $networkService)
    {
        $this->client = $client;
        $this->networkService = $networkService;
    }

    public function run(string $network, string $redirectUri): ?CookieJarInterface
    {
        $this->cookies = new TimCookieJar();
        $localJar      = new TimCookieJar();
        $stack         = $this->configureRequestStack();
        $networkEntity = $this->networkService->findOneBySlug($network);

        $client = $this->client->build(
            [
                'base_uri' => TimBR::getOAMUri(),
                'allow_redirects' => true,
                'track_redirects' => true,
                'cookies' => true,
                'verify' => false,
                'handler' => $stack
            ]
        );

        $header        = TimHeadersFactory::make($network);
        $clientId      = $header->getClientId();
        $clientNetwork = in_array($network, NetworkCustomClientsTimEnum::CUSTOM_CLIENTS, true) ? $clientId : $network;
        $query         = '?response_type=code';
        $query        .= '&client_id=' . $clientNetwork;
        $query        .= '&redirect_uri=' . urlencode($redirectUri);
        $query        .= '&scope=' . TimBR::getAuthScopesByNetwork($networkEntity);

        $client->httpGet(AuthenticationRoutes::AUTHORIZE . $query, ['cookies' => $localJar]);

        $this->setGlobalCookie();

        if ($this->validateCookies()) {
            return $this->cookies;
        }

        throw new TimBRAuthenticationInvalidCookies();
    }

    protected function configureRequestStack(): HandlerStack
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::cookies());
        $stack->push(Middleware::redirect());
        $stack->push(
            Middleware::mapRequest(
                function (RequestInterface $request) {
                    return $request;
                }
            )
        );

        $stack->push(
            Middleware::mapResponse(
                function (ResponseInterface $response) {
                    $headers                 = $response->getHeaders();
                    $this->arrayCookies[]    = data_get($headers, 'Set-Cookie');
                    $this->redirectHistory[] = data_get($headers, 'Location');

                    return $response;
                }
            )
        );

        $stack->push(Middleware::cookies());

        return $stack;
    }

    private function setGlobalCookie(): void
    {
        $this->arrayCookies = array_filter($this->arrayCookies);
        foreach ($this->arrayCookies as $setOfCookies) {
            foreach ($setOfCookies as $cookie) {
                $sc = SetCookie::fromString($cookie);
                if (! $sc->getDomain()) {
                    $sc->setDomain('.com.br');
                }
                $this->cookies->setCookie($sc);
            }
        }
    }

    private function validateCookies(): bool
    {
        $validCookies = array_filter(
            $this->cookies->toArray(),
            function ($cookie) {
                return $this->validatePregMatch($cookie);
            }
        );

        return count($validCookies) === count(self::MANDATORY_COOKIES);
    }


    private function validatePregMatch($cookie): bool
    {
        foreach (self::MANDATORY_COOKIES as $MANDATORYCOOKIE) {
            if (str_contains(data_get($cookie, 'Name'), $MANDATORYCOOKIE)) {
                return true;
            }
        }
        return false;
    }
}
