<?php

namespace Authorization\Services;

use Illuminate\Support\Facades\Log;

class ThirdPartyAccessConfig
{
    public function getByClient(string $client): ?array
    {
        $thirdPartyConfig = config('thirdPartiesAccess.'. $client);

        if (is_array($thirdPartyConfig)) {
            return $thirdPartyConfig;
        }

        Log::alert('Invalid Configuration for Client', ['client' => $client]);
        return null;
    }
}
