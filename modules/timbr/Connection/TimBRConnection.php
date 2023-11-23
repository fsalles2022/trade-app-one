<?php

namespace TimBR\Connection;

use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use TimBR\Adapters\TimBRElegibilityRequestAdapterV3;
use TimBR\Connection\Authentication\AuthenticationConnection;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Enumerators\TimBRFormats;
use TimBR\Exceptions\TimBRGenerateProtocolException;
use TimBR\Exceptions\TimBROrder;
use TimBR\Exceptions\TimBROrderApprovalException;
use TimBR\Exceptions\TimBRSimCardActivationException;
use TimBR\Models\Eligibility;
use TradeAppOne\Domain\HttpClients\Responseable;

class TimBRConnection
{
    /**
     * @var TimBRHttpClient
     */
    protected $client;
    /**
     * @var AuthenticationConnection
     */
    protected $authenticationNetwork;

    public function __construct(AuthenticationConnection $authenticationConnection)
    {
        $this->authenticationNetwork = $authenticationConnection;
    }

    public function selectCustomConnection(string $connection): TimBRConnection
    {
        $this->client = $this->authenticationNetwork->authenticateNetwork($connection);
        return $this;
    }

    public function getDomains($network, $cpf): Collection
    {
        $domains = Cache::get(TimBRCacheables::DOMAINS);
        if ($domains === null) {
            $domains           = $this->authenticationNetwork->getClient($network, $cpf)->get(TimBRRoutes::DOMAINS)->toArray();
            $domainsCollection = collect($domains);
            Cache::put(TimBRCacheables::DOMAINS, $domainsCollection, 1440);
            return $domainsCollection;
        }
        return $domains;
    }

    public function eligibility(string $network, string $cpf, array $payload): Responseable
    {
        $payload['transactionToken'] = $this->authenticationNetwork->transactionToken($network, $cpf);

        $adapted = TimBRElegibilityRequestAdapterV3::adapt($payload);

        $response = $this->authenticationNetwork->getPMIDClient($network, $cpf)->post(TimBRRoutes::ELIGIBILITY, $adapted, ['AuthorizationOAM' => 'Bearer ' . $this->authenticationNetwork->getBearerToken($network, $cpf)]);

        try {
            $eligibility   = new Eligibility();
            $arrayResponse = $response->toArray();

            $eligibility->products         = collect($arrayResponse['products']);
            $eligibility->eligibilityToken = $arrayResponse['eligibilityToken'];

            $customerCpf = $payload['customer']['cpf'];
            Cache::put(TimBRCacheables::ELIGIBILITY . $customerCpf, $eligibility, TimBRCacheables::ELIGIBILITY_DUE);
            return $response;
        } catch (ErrorException $exception) {
            return $response;
        }
    }

    /** Check master MSISDN in Dependent flow */
    public function checkMasterMsisdn(string $network, string $cpf, string $masterMsisdn, $payload): Responseable
    {
        $response = $this->authenticationNetwork->getClientForOrder($network, $cpf)->get(str_replace('{{MSISDN}}', $masterMsisdn, TimBRRoutes::CUSTOMER_NUMBER_VALIDATION), $payload);

        throw_if(
            $response->getStatus() !== Response::HTTP_OK,
            new TimBROrder($response, $response->getStatus(), $response->get('message'))
        );

        return $response;
    }

    public function order(string $network, string $cpf, $payload): Responseable
    {
        $response = $this->authenticationNetwork->getClientForOrder($network, $cpf)->post(TimBRRoutes::ORDER, $payload);

        throw_if(
            $response->getStatus() !== Response::HTTP_OK,
            new TimBROrder($response, $response->getStatus(), $response->get('message'))
        );

        return $response;
    }

    /** Verify Crivo 3 (Credit Analysis) for customer */
    public function orderApproval(string $network, string $cpf, $payload): Responseable
    {
        $response = $this->authenticationNetwork->getClientForOrder($network, $cpf)->post(TimBRRoutes::ORDER_APPROVAL, $payload);

        throw_if(
            $response->getStatus() !== Response::HTTP_OK,
            new TimBROrderApprovalException($response->getStatus(), $response->get('message'))
        );

        return $response;
    }

    /** Allocate msisdn in SIMCARD (ICCID) */
    public function simCardActivation(string $network, string $cpf, $payload): Responseable
    {
        $response = $this->authenticationNetwork->getPMIDClient($network, $cpf)->post(TimBRRoutes::SIMCARD_ACTIVATION, $payload, [ 'clientId' => 'TIMVENDAS' ]);

        throw_if(
            $response->getStatus() !== Response::HTTP_OK,
            new TimBRSimCardActivationException($response->getStatus(), $response->get('provider.errorMessage'))
        );

        return $response;
    }

    public function getCep(string $network, string $cpf, string $cep = ''): Responseable
    {
        return $this->authenticationNetwork->getClient($network, $cpf)->get(TimBRRoutes::CEP, [
            'postCode' => $cep
        ]);
    }

    public function getOrderStatus(Carbon $day): Responseable
    {
        $initialDay = $day->format(TimBRFormats::STATUS_DATE_FORMAT);
        $finalDay   = $day->addDay()->format(TimBRFormats::STATUS_DATE_FORMAT);
        $query      = "{$initialDay}/{$finalDay}";

        return $this->client->get(TimBRRoutes::ORDER_STATUS . $query);
    }

    public function getOrderStatusByProtocol(string $protocol): Responseable
    {
        return $this->client->get(TimBRRoutes::ORDER_STATUS_PROTOCOL . $protocol);
    }

    public function generateProtocol(string $network, string $cpf, $payload): Responseable
    {
        $response = $this->authenticationNetwork->getPMIDClient($network, $cpf)->post(TimBRRoutes::GENERATE_PROTOCOL, $payload);

        throw_if(
            $response->getStatus() !== Response::HTTP_OK,
            new TimBRGenerateProtocolException($response->getStatus(), $response->get('provider.errorMessage'))
        );

        return $response;
    }
}
