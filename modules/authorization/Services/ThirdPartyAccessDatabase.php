<?php

namespace Authorization\Services;

use Authorization\Models\Integration;
use Illuminate\Support\Facades\Log;

class ThirdPartyAccessDatabase
{
    public function getByAccessKey(string $accessKey)
    {
        $thirdParty = Integration::query()->where('accessKey', $accessKey)->first();

        if ($thirdParty instanceof Integration) {
            return $thirdParty;
        }

        Log::alert('Invalid Configuration for Client', ['accessKey' => $accessKey]);
        return null;
    }
}
