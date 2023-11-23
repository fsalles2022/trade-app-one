<?php

namespace Authorization\Models;

use Authorization\Exceptions\OriginNotFoundInWhiteListException;
use Authorization\Exceptions\RouteNotAvailableException;
use Authorization\Services\ThirdPartyAuth;
use TradeAppOne\Domain\Models\Tables\User;

class ThirdPartyClient
{
    private $accessKey;
    private $accessUser;
    private $accessWhiteList;
    private $routes;

    public function __construct(string $accessKey, User $accessUser, array $accessWhiteList, array $routes)
    {
        $this->accessKey       = $accessKey;
        $this->accessUser      = $accessUser;
        $this->accessWhiteList = $accessWhiteList;
        $this->routes          = $routes;
    }

    public function withIp(string $ip = ""): ThirdPartyClient
    {
        if (in_array("*", $this->accessWhiteList)) {
            return $this;
        }

        if (in_array($ip, $this->accessWhiteList)) {
            return $this;
        }

        throw new OriginNotFoundInWhiteListException();
    }

    public function withRoute(string $pathReceived = "", string $methodSent = ""): ThirdPartyClient
    {
        foreach ($this->routes as $route) {
            $dbURI   = preg_replace("/:\w*:/", '\w+-?\d*', $route['uri']);
            $pattern = str_replace('/', '\/', $dbURI);
            preg_match("/^{$pattern}\/{0}$/", $pathReceived, $urlMatch);

            if (! empty($urlMatch) && $route['method'] === $methodSent) {
                return $this;
            }
        }

        throw new RouteNotAvailableException();
    }

    public function retriveBearerToken()
    {
        $thirdPartyAuth = new ThirdPartyAuth();

        return "Bearer " . $thirdPartyAuth->retrieveBearerToken($this->accessUser);
    }

    public function isSameAccessKey(string $accessKey): bool
    {
        return ($this->accessKey === $accessKey);
    }
}
