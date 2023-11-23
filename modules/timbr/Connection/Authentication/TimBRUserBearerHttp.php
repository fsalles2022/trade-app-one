<?php

namespace TimBR\Connection\Authentication;

use Exception;
use TimBR\Connection\Authentication\TimBRUserAuthentication\AuthenticationFirstStep;
use TimBR\Connection\Authentication\TimBRUserAuthentication\AuthenticationSecondStep;
use TimBR\Connection\Authentication\TimBRUserAuthentication\TimBRClientUserAuthentication;
use TimBR\Connection\TimBR;
use TimBR\Exceptions\TimBRAuthenticationFailed;
use TimBR\Exceptions\TimBRAuthenticationInvalidCookies;

class TimBRUserBearerHttp
{
    protected $firstStep;
    protected $secondStep;
    protected $clientUserAuthentication;

    public function __construct(
        AuthenticationFirstStep $firstStep,
        AuthenticationSecondStep $secondStep,
        TimBRClientUserAuthentication $clientUserAuthentication
    ) {
        $this->firstStep                = $firstStep;
        $this->secondStep               = $secondStep;
        $this->clientUserAuthentication = $clientUserAuthentication;
    }

    public function requestBearer(string $network, string $encryptedCpf, string $redirectUri, string $basic): array
    {
        try {
            $cookies = $this->firstStep->run($network, $redirectUri);

            if ($cookies !== null) {
                $code     = $this->secondStep->run($cookies, $encryptedCpf);
                $response = $this->exchangeCodeForBearer($code, $redirectUri, $basic);

                return [
                    data_get($response, 'access_token'),
                    data_get($response, 'expires_in'),
                ];
            }
            return [];
        } catch (TimBRAuthenticationInvalidCookies $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new TimBRAuthenticationFailed($exception->getMessage());
        }
    }

    private function exchangeCodeForBearer(string $code, string $redirectUri, string $basic): array
    {
        $client  = $this->clientUserAuthentication->build(
            [
                'base_uri' => TimBR::getOAMUri(),
                'headers' => [
                    'Authorization' => $basic,
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
                ],
                'cookies' => true,
                'verify' => false,
            ]
        );
        $options = [
            'form_params' => [
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
                'code' => $code
            ],
            'headers' => [
                'Authorization' => $basic,
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ]
        ];
        $url     = AuthenticationRoutes::FIRST_STEP;
        return $client->httpPost($url, $options)->toArray();
    }
}
