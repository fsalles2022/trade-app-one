<?php
declare(strict_types=1);

namespace ClaroBR\Connection;

use ClaroBR\Adapters\Siv3PayloadAdapter;
use ClaroBR\Enumerators\ClaroBRCaches;
use ClaroBR\Exceptions\Siv3Exceptions;
use ClaroBR\Siv3Headers;
use Illuminate\Support\Facades\Cache;
use Throwable;
use TradeAppOne\Domain\HttpClients\Responseable;

class Siv3Connection
{
    /** @var Siv3HttpClient */
    private $sivClient;

    /** @var Siv3Headers */
    private $sivHeaders;

    public function __construct(Siv3HttpClient $sivClient, Siv3Headers $sivHeaders)
    {
        $this->sivClient  = $sivClient;
        $this->sivHeaders = $sivHeaders;
    }

    /** @throws Throwable */
    public function authenticate(): Responseable
    {
        $response = $this->sivClient->post(Siv3Routes::ENDPOINT_AUTHENTICATE, $this->sivHeaders->getCredentials(), []);

        throw_unless($response->get('token'), Siv3Exceptions::invalidCredentials());

        return $response;
    }

    public function getToken(): string
    {
        $data = Cache::remember(
            ClaroBRCaches::SIV3_USER_BEARER,
            ClaroBRCaches::SIV3_AUTHENTICATION_DUE,
            function () {
                return $this->authenticate()->toArray();
            }
        );

        return $data['token'] ?? '';
    }

    /** @param mixed[] $attributes */
    public function checkSale(array $attributes): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->post(Siv3Routes::ENDPOINT_CHECK_EXTERNAL_SALE, $attributes);
    }

    /** @param mixed[] $attributes */
    public function createSale(array $attributes): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->post(Siv3Routes::ENDPOINT_CREATE_EXTERNAL_SALE, $attributes);
    }

    /** @param mixed[] $attributes */
    public function getSalesToReport(array $attributes): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->post(Siv3Routes::ENDPOINT_EXPORT_EXTERNAL_SALE, $attributes);
    }

    public function getAddressByPostalCode(string $postalCode): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->getWithBody(Siv3Routes::ADDRESS_BY_POSTAL_CODE, [
            'cep' => $postalCode
        ]);
    }

    /**
     * @param mixed[] $attributes
     */
    public function getResidentialCreditAnalysis(array $attributes): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->getWithBody(Siv3Routes::RESIDENTIAL_CREDIT_ANALYSIS, $attributes);
    }

    /**
     * @param mixed[] $attributes
     */
    public function postResidentialProposal(array $attributes): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->post(Siv3Routes::ENDPOINT_RESIDENTIAL_PROPOSAL_CREATE, $attributes);
    }

    public function getAddressesByPostalCode(string $postalCode): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->getWithBody(Siv3Routes::ENDPOINT_ADDRESS, [
            'postalCode' => $postalCode,
        ]);
    }

    public function sendAuthorization(Siv3PayloadAdapter $siv3AdapterPayload): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->post(Siv3Routes::ENDPOINT_SEND_AUTHORIZATION, $siv3AdapterPayload->jsonSerialize());
    }

    public function checkAuthorizationCode(Siv3PayloadAdapter $siv3PayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->sivClient->post(Siv3Routes::ENDPOINT_CHECK_AUTHORIZATION, $siv3PayloadAdapter->jsonSerialize());
    }

    private function pushTokenHeader(): void
    {
        $this->sivClient->pushHeader([
            'token' => $this->getToken(),
        ]);
    }
}
