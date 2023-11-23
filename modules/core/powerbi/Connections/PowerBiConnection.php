<?php

namespace Core\PowerBi\Connections;

use Core\PowerBi\Constants\PowerBiDashboards;
use Core\PowerBi\Exceptions\PowerBiExceptions;
use Illuminate\Support\Facades\Cache;

class PowerBiConnection
{
    private const PBI_CACHE_CLIENT = 'PBI_CACHE_CLIENT';

    protected $client;

    public function __construct(PowerBiClient $client)
    {
        $this->client = $client;
    }

    public function getDashboard(array $dashboard): array
    {
        $groupId  = $dashboard[PowerBiDashboards::GROUP];
        $reportId = $dashboard[PowerBiDashboards::REPORT];

        return [
            'type'        => 'report',
            'accessToken' => $this->embedToken($groupId, $reportId),
            'embedUrl'    => $this->embedUrl($groupId, $reportId),
            'id'          => $reportId
        ];
    }

    public function embedToken(string $groupId, string $reportId): string
    {
        $uri = config('pbi.url'). "/v1.0/myorg/groups/$groupId/reports/$reportId/GenerateToken";

        $headers = [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json',
        ];

        $form = [
            'accessLevel' => 'View',
            'allowSaveAs' => 'false',
        ];

        $result = $this->client->postFormParams($uri, $form, $headers);


        if ($token = $result->get('token')) {
            return $token;
        }

        throw PowerBiExceptions::errorGenerateToken($result);
    }

    public function embedUrl(string $groupId, string $reportId): string
    {
        $url = config('pbi.report_url');
        return "$url?reportId=$reportId&groupId=$groupId";
    }

    private function getToken(): string
    {
        if ($token = Cache::get(self::PBI_CACHE_CLIENT)) {
            return $token;
        }

        return $this->login();
    }

    public function login(): string
    {
        $uri     = config('pbi.auth_url'). '/common/oauth2/token';
        $options = config('pbi.auth');

        $result = $this->client->postFormParams($uri, $options);

        if ($token = $result->get('access_token')) {
            $expires_in = $result->get('expires_in')/60;
            Cache::put(self::PBI_CACHE_CLIENT, $token, $expires_in);
            return $token;
        }

        throw PowerBiExceptions::errorGenerateToken($result);
    }
}
