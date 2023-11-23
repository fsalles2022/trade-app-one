<?php

namespace TimBR\Connection\Authentication\TimBRUserAuthentication;

use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TimBR\Connection\Authentication\AuthenticationRoutes;
use TimBR\Connection\TimBR;
use TimBR\Exceptions\TimBRAuthenticationFailed;

class AuthenticationSecondStep
{
    const COOKIE_CATCHED_FOR_AUTHENTICATION = 'OAMAuthnCookie_authb2b2c.tim.com.br:443';
    const INDEX_OF_HISTORY_WITH_CODE        = '2.0';
    /**
     * @var CookieJarInterface
     */
    protected $cookies;
    protected $redirectHistory = [];
    protected $authNCookie;
    protected $arrayCookies = [];
    protected $authenticationClient;

    public function __construct(TimBRClientUserAuthentication $client)
    {
        $this->authenticationClient = $client;
    }

    public function run(CookieJarInterface $cookies, $encryptedCpf): string
    {
        $this->cookies = $cookies;
        $stack2        = $this->configureRequestStack();

        $client2 = $this->authenticationClient->build([
            'base_uri'        => TimBR::getOAMUri(),
            'allow_redirects' => true,
            'strict'          => false,
            'track_redirects' => true,
            'cookies'         => true,
            'verify'          => false,
            'handler'         => $stack2,
            'headers'         => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ]
        ]);

        $client2->httpPost(AuthenticationRoutes::CRED_SUBMIT, [
            'form_params' => [
                'content'    => $encryptedCpf,
                'request_id' => '21222343666'
            ],
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ],
            'cookies'     => $this->cookies
        ]);

        return $this->extractCodeFromRedirectHistory();
    }

    protected function configureRequestStack(): HandlerStack
    {
        $stack2 = new HandlerStack();
        $stack2->setHandler(new CurlHandler());

        $stack2->push(Middleware::cookies());
        $stack2->push(Middleware::redirect());

        $stack2->push(Middleware::mapRequest(function (RequestInterface $request) {
            if ($this->authNCookie) {
                $this->cookies->setCookie($this->authNCookie);
                return $this->cookies->withCookieHeader($request);
            }
            return $request;
        }));

        $stack2->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $headers = $response->getHeaders();
            array_push($this->redirectHistory, data_get($headers, 'Location'));
            array_push($this->arrayCookies, data_get($headers, 'Set-Cookie'));
            if (str_contains(data_get($this->arrayCookies, '1.1'), self::COOKIE_CATCHED_FOR_AUTHENTICATION)) {
                $this->authNCookie = SetCookie::fromString(data_get($this->arrayCookies, '1.1'));
            }

            return $response;
        }));

        return $stack2;
    }

    public function extractCodeFromRedirectHistory(): string
    {
        $redirectToTradeAppOne = data_get($this->redirectHistory, self::INDEX_OF_HISTORY_WITH_CODE);
        if (str_contains($redirectToTradeAppOne, '?code=')) {
            $uri        = new Uri($redirectToTradeAppOne);
            $query      = $uri->getQuery();
            $attributes = explode('=', $query);
            if (data_get($attributes, '0') == 'code') {
                return data_get($attributes, '1');
            }
        }
        throw new TimBRAuthenticationFailed();
    }

    private function validateCookies()
    {
        if ($this->cookies->count() == count(self::MANDATORY_COOKIES)) {
            foreach ($this->cookies->toArray() as $cookie) {
                if ($this->validatePregMatch($cookie) == false) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    private function validatePregMatch($cookie)
    {
        foreach (self::MANDATORY_COOKIES as $MANDATORYCOOKIE) {
            if (str_contains(data_get($cookie, 'Name'), $MANDATORYCOOKIE)) {
                return true;
            }
        }
        return false;
    }
}
