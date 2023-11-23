<?php

namespace Authorization\Services;

use Authorization\Models\ThirdPartyClient;

class ThirdPartiesAccessFactory
{
    public function getByAccessKey(string $accessKeyInformed): ?ThirdPartyClient
    {
        $thirdParty = $this->getFromDatabase($accessKeyInformed);

        if ($thirdParty instanceof ThirdPartyClient && $thirdParty->isSameAccessKey($accessKeyInformed)) {
            return $thirdParty;
        }

        return null;
    }

    private function getFromDatabase(string $accessKey)
    {
        $integration = (resolve(ThirdPartyAccessDatabase::class))->getByAccessKey($accessKey);

        $thirdPartyAccessKey = data_get($integration, 'accessKey', null);
        $userAccess          = data_get($integration, 'user', null);
        $accessWhiteList     = data_get($integration, 'whitelist', []);
        $routes              = data_get($integration, 'routes', []);

        $canCreateInstance = filled($accessWhiteList) && filled($routes) && filled($userAccess);

        if ($canCreateInstance) {
            $routes          = $routes->toArray();
            $accessWhiteList = $accessWhiteList->pluck('ip')->toArray();

            return new ThirdPartyClient($thirdPartyAccessKey, $userAccess, $accessWhiteList, $routes);
        }

        return null;
    }
}
