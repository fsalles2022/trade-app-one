<?php

namespace VivoBR\Connection;

use TradeAppOne\Domain\Components\RestClient\Rest;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Responseable;
use VivoBR\Connection\Headers\AvenidaSunHeaders;
use VivoBR\Connection\Headers\CasaEVideoSunHeaders;
use VivoBR\Connection\Headers\CeaSunHeaders;
use VivoBR\Connection\Headers\EletrozemaSunHeaders;
use VivoBR\Connection\Headers\ExtraSunHeaders;
use VivoBR\Connection\Headers\FastShopSunHeaders;
use VivoBR\Connection\Headers\FuijokaSunHeaders;
use VivoBR\Connection\Headers\HervalSunHeaders;
use VivoBR\Connection\Headers\LebesSunHeaders;
use VivoBR\Connection\Headers\PernambucanasSunHeaders;
use VivoBR\Connection\Headers\RiachueloSunHeaders;
use VivoBR\Connection\Headers\SchumannSunHeaders;
use VivoBR\Exceptions\SunNoAccessException;
use VivoBR\Exceptions\SunUnavailableException;

class SunConnection
{
    public const HEADERS = [
        NetworkEnum::CEA           => CeaSunHeaders::class,
        NetworkEnum::RIACHUELO     => RiachueloSunHeaders::class,
        NetworkEnum::IPLACE        => HervalSunHeaders::class,
        NetworkEnum::TAQI          => HervalSunHeaders::class,
        NetworkEnum::PERNAMBUCANAS => PernambucanasSunHeaders::class,
        NetworkEnum::LEBES         => LebesSunHeaders::class,
        NetworkEnum::EXTRA         => ExtraSunHeaders::class,
        NetworkEnum::SCHUMANN      => SchumannSunHeaders::class,
        NetworkEnum::ELETROZEMA    => EletrozemaSunHeaders::class,
        NetworkEnum::FUJIOKA       => FuijokaSunHeaders::class,
        NetworkEnum::CASAEVIDEO    => CasaEVideoSunHeaders::class,
        NetworkEnum::FAST_SHOP     => FastShopSunHeaders::class,
        NetworkEnum::AVENIDA       => AvenidaSunHeaders::class,
    ];
    /**
     * @var SunHttpClient
     */
    protected $client;

    public function __construct(Rest $client)
    {
        $this->client = $client;
    }

    public function selectCustomConnection(string $network)
    {
        try {
            $selected = app()->make(self::HEADERS[$network]);
        } catch (\Exception $exception) {
            throw new SunNoAccessException();
        }

        $this->client->addHeaders($selected->getHeaders());
        return $this;
    }

    public function home(): Responseable
    {
        return $this->client->get('')->execute();
    }

    public function listPlans(array $query = []): Responseable
    {
        return $this->client->get(SunRoutes::LIST_PLANS)->withQuery($query)->execute();
    }

    public function createUser(array $attributes): Responseable
    {
        return $this->client->post(SunRoutes::USER)->withData($attributes)->execute();
    }

    public function updateUser(array $attributes): Responseable
    {
        return $this->client
            ->post(SunRoutes::USER . '/' . data_get($attributes, 'cpf'))
            ->withData($attributes)
            ->execute();
    }

    public function getUser(string $cpf): Responseable
    {
        return $this->client->get(SunRoutes::USER . '/' . $cpf)->execute();
    }

    public function salePre($body): Responseable
    {
        return $this->client->post(SunRoutes::SALES_PRE_PAGO)->withData($body)->execute();
    }

    public function sale($body): Responseable
    {
        $start    = microtime(true);
        $response = $this->client->post(SunRoutes::SALES)->withData($body)->execute();
        heimdallLog()
            ->start($start)
            ->end(microtime(true))
            ->realm(Operations::VIVO)
            ->request($body)
            ->response($response)
            ->fire();
        return $response;
    }

    public function salePrePago($body): Responseable
    {
        return $this->client->post(SunRoutes::SALES_PRE_PAGO)->withData($body)->execute();
    }

    public function confirmControleCartao($body): Responseable
    {
        return $this->client->post(SunRoutes::UPDATE_STATUS_CONTROLE_CARTAO)->withData($body)->execute();
    }

    public function querySales(array $query = []): Responseable
    {
        return $this->client->get(SunRoutes::QUERY_SALE)->withQuery($query)->execute();
    }

    public function portabilityOperators(): Responseable
    {
        return $this->client->get(SunRoutes::PORTABILITY_OPERATORS)->execute();
    }

    public function customerTotalization(string $cpf): Responseable
    {
        return  $this->client->get(SunRoutes::TOTALIZATION . $cpf)->execute();
    }
}
