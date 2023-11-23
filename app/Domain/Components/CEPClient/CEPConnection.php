<?php

namespace TradeAppOne\Domain\Components\CEPClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Response;

class CEPConnection
{
    protected $viaCep;
    protected $webMania;

    public function get(string $cep = '')
    {
        $this->viaCep   = new Client(['base_uri' => config('utils.viaCep.uri', '')]);
        $this->webMania = new Client(['base_uri' => config('utils.webMania.uri', '')]);
        try {
            $response      = $this->viaCep->get("/ws/{$cep}/json");
            $return        = (string) $response->getBody();
            $arrayResponse = json_decode($return, true);
            if (isset($arrayResponse['erro'])) {
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            } else {
                $status = Response::HTTP_OK;
            }
        } catch (RequestException $exception) {
            $response      = $this->webMania->get("api/1/cep/{$cep}/json", [
                'query' => [
                    'app_key'    => config('utils.webMania.key', ''),
                    'app_secret' => config('utils.webMania.secret', ''),
                ]
            ]);
            $return        = (string) $response->getBody();
            $arrayResponse = json_decode($return, true);
            if (empty($arrayResponse['uf'])) {
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            } else {
                $status = Response::HTTP_OK;
            }
        }
        return [$arrayResponse, $status];
    }
}
