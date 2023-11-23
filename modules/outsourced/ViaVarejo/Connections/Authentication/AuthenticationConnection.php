<?php


namespace Outsourced\ViaVarejo\Connections\Authentication;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Outsourced\Partner\Services\Clients\ViaVarejoPartnerAuthentication;
use Outsourced\ViaVarejo\Connections\Headers\ViaVarejoHeaders;
use Outsourced\ViaVarejo\Connections\ViaVarejoHttpClient;

class AuthenticationConnection
{
    protected $viaVarejoHeaders;

    public function __construct(ViaVarejoHeaders $viaVarejoHeaders)
    {
        $this->viaVarejoHeaders = $viaVarejoHeaders;
    }

    public function auth(): ViaVarejoHttpClient
    {
        $accessTokenViaVarejo = Cache::get(ViaVarejoPartnerAuthentication::VIA_VAREJO_ACCESS_TOKEN_KEY);
        $client               = new Client([
            'verify'   => false,
            'base_uri' => $this->viaVarejoHeaders->getUri(),
            'headers'  => [
                'Authorization' => 'bearer ' . $accessTokenViaVarejo,
            ]
        ]);

        return $this->newHttpClient($client);
    }

    public function newHttpClient($client): ViaVarejoHttpClient
    {
        return app()->makeWith(ViaVarejoHttpClient::class, ['client' => $client]);
    }
}
