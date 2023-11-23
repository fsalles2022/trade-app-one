<?php

namespace ClaroBR\Connection;

use ClaroBR\Adapters\AutomaticRegistrationSendAdapter;
use ClaroBR\Enumerators\ClaroBRCaches;
use ClaroBR\Exceptions\AttributeNotFound;
use ClaroBR\Exceptions\NoAccessToSivException;
use ClaroBR\Exceptions\SivAuthExceptions;
use ClaroBR\Exceptions\SivInvalidCredentialsException;
use ErrorException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Tables\User;

class SivConnection implements SivConnectionInterface
{
    protected $sivClient;
    protected $sivRoutes;
    protected $bearer;
    protected $user;
    protected $siv;

    public const HIGH_QUANTITY_OF_PLANS = 10000000000;

    public function __construct(SivRoutes $sivRoutes, SivHttpClient $sivConnectionResolver)
    {
        $this->sivRoutes = $sivRoutes;
        $this->sivClient = $sivConnectionResolver;
    }

    /** @throws */
    public function plans(array $filters = [], ?User $user = null): Responseable
    {
        $this->authenticate($user);
        $filters += [ 'items_per_page' => self::HIGH_QUANTITY_OF_PLANS];
        return $this->sivClient->get(SivRoutes::LIST_PLANS, $filters);
    }

    /** @throws */
    public function authenticate(?User $user = null)
    {
        $this->user = $user ?? Auth::user();

        try {
            $cpf = data_get($this->user, 'cpf', '');
            $this->sivClient->authenticate($cpf);
            return true;
        } catch (DecryptException $exception) {
            throw new SivInvalidCredentialsException();
        }
    }

    public function promoterAuth(array $attributes): array
    {
        $response        = $this->sivClient
            ->post(SivRoutes::AUTH_PROMOTER, ['promoter_form' => array_filter($attributes)]);
        $responseMessage = data_get($response->toArray(), 'message');

        if (str_contains($responseMessage, trans('siv::exceptions.' . SivAuthExceptions::SEND_PROMOTER_TOKEN))) {
            throw SivAuthExceptions::sendPromoterToken($responseMessage);
        }

        if (str_contains($responseMessage, trans('siv::exceptions.' . SivAuthExceptions::FIRST_ACCESS_PROMOTER))) {
            $userName = data_get($attributes, 'username');
            Cache::put(ClaroBRCaches::SIV_PROMOTER_FIRST_AUTH. $userName, true, 1000);
            throw SivAuthExceptions::firstAccessPromoter($responseMessage);
        }

        if (str_contains($responseMessage, trans('siv::exceptions.' . SivAuthExceptions::SELECT_PDV))) {
            throw SivAuthExceptions::needSelectPDV($response->get('data'));
        }

        if ($response->getStatus() === Response::HTTP_BAD_REQUEST) {
            throw SivAuthExceptions::default($responseMessage);
        }

        if ($response->get('data.token') === null) {
            throw SivAuthExceptions::tokenNotFound($response->toArray());
        }

        return $response->get('data');
    }

    public function setBearer(string $bearer): SivConnection
    {
        $this->sivClient->pushHeader(['Authorization' => 'Bearer ' . $bearer]);
        return $this;
    }

    /** @throws */
    public function sale(array $customer, array $services, $extra = null): Responseable
    {
        $this->authenticate();

        $userPointOfSale = Auth::user()->pointsOfSale->first();
        $sivId           = data_get($userPointOfSale->providerIdentifiers, Operations::CLARO);
        $userSaleCpf     = data_get($extra, 'cpf', '');

        $pointOfSaleSiv = $this->getIdentifiers(SivRoutes::ENDPOINT_PDV_USER, $userSaleCpf)->toArray();
        $sivUser        = $this->getIdentifiers(SivRoutes::ENDPOINT_USER . '/', $userSaleCpf)->toArray();

        $pointsOfSale  = data_get($pointOfSaleSiv, 'data', []);
        $pointOfSaleId = data_get($pointsOfSale, 'id', '');

        if (! $pointOfSaleId && is_array($pointsOfSale)) {
            if (count($pointsOfSale) === 1) {
                $pointOfSaleId = data_get($pointsOfSale, '0.id');
            } else {
                $pointOfSaleId = data_get(array_first(array_filter($pointsOfSale, function ($pointOfSale) use ($sivId) {
                    return data_get($pointOfSale, 'codigo') === $sivId;
                })), 'id', '');
            }
        }

        $payload = [
            'sale_form'     => array_filter([
                'pdv_id'      => $pointOfSaleId,
                'usuario_id'  => data_get($sivUser, 'data.id', ''),
                'promotor_id' => (int) Cache::get(ClaroBRCaches::PROMOTOR_ID . Auth::user()->cpf)
            ]),
            'customer_form' => $customer,
            'service_form'  => [$services]
        ];

        return $this->sivClient->post($this->sivRoutes::ENDPOINT_SALES, $payload);
    }

    public function getIdentifiers(string $route, string $cpf): Responseable
    {
        try {
            $response = $this->sivClient->get($route . $cpf);
        } catch (ErrorException $exception) {
            throw  new NoAccessToSivException();
        }
        return $response;
    }

    /** @throws */
    public function logSale($servicePayload, $sale): Responseable
    {
        $this->authenticate();

        return $this->sivClient->put($this->sivRoutes::ENDPOINT_SALES . "/{$sale}", $servicePayload);
    }

    /** @throws */
    public function utils(): Responseable
    {
        $this->authenticate();

        return $this->sivClient->get($this->sivRoutes::UTILS);
    }

    /** @throws */
    public function activate(string $servicoId, ?string $selectedMsisdn, array $extraPayload = []): Responseable
    {
        $this->authenticate();
        $activeForm['servico_id'] = $servicoId;
        if (! empty($selectedMsisdn) && (Auth::user()->isInovaPromoter()  || Auth::user()->isDistribuicaoChannel())) {
            $activeForm['msisdn_inova'] = $selectedMsisdn;
        }
        $activeForm      = array_merge($activeForm, $extraPayload);
        $payloadFormated = [
            'active_form'  => $activeForm,
            'options_form' => array_filter([
                'uri_m4u_callback' => '*',
                'token'            => data_get($extraPayload, 'token', null)
            ])
        ];

        return $this->sivClient->post(SivRoutes::ENDPOINT_ACTIVATION, $payloadFormated);
    }

    /** @throws */
    public function getUserSiv($cpf, $password)
    {
        try {
            if ($cpf && $password) {
                $this->bearer = $this->sivClient->authenticate($cpf);
                $sivUser      = $this->sivClient->get($this->sivRoutes::ENDPOINT_USER  .'/'. $cpf);
                return $sivUser->toArray()['data'];
            }
            return null;
        } catch (ErrorException $exception) {
            throw new SivInvalidCredentialsException();
        }
    }

    public function creditAnalysis(array $form): Responseable
    {
        $this->authenticate();

        $name = data_get($form, 'firstName') . ' ' . data_get($form, 'lastName');

        $payloadFormated = [
            'credit_form' => array_filter([
                'cpf'             => data_get($form, 'cpf'),
                'nome'            => filled($name) ? $name : null,
                'data_nascimento' => data_get($form, 'birthday'),
                'cidade'          => data_get($form, 'city'),
                'logradouro'      => data_get($form, 'local'),
                'bairro'          => data_get($form, 'neighborhood'),
                'uf'              => data_get($form, 'state'),
                'cep'             => data_get($form, 'zipCode'),
                'numero'          => data_get($form, 'number'),
                'filiacao'        => data_get($form, 'filiation')
            ])
        ];

        return $this->sivClient->post(SivRoutes::ENDPOINT_CREDIT_ANALYSIS, $payloadFormated);
    }

    public function querySales(array $query = []): Responseable
    {
        $sentinel = config('integrations.siv.sentinel');
        if ($sentinel) {
            $this->sivClient->authenticate($sentinel);
            return $this->sivClient->get(SivRoutes::ENDPOINT_SALES, $query);
        }
        throw new SivInvalidCredentialsException();
    }

    public function m4UByServiceId(string $serviceId)
    {
        $sentinel = config('integrations.siv.sentinel');
        $this->sivClient->authenticate($sentinel);
        return $this->sivClient->get(SivRoutes::M4U_BY_SERVICE_ID . $serviceId);
    }

    public function analiseAuthenticate(array $attributes): Responseable
    {
        $this->authenticate();
        return $this->sivClient->post(SivRoutes::ENDPOINT_AUTHENTICATE, $attributes);
    }

    public function statusAuthenticate(array $attributes): Responseable
    {
        $this->authenticate();
        return $this->sivClient->get(SivRoutes::ENDPOINT_STATUS_AUTHENTICATE, $attributes);
    }

    /**
     * @param mixed[] $attributes
     * @return Responseable
     * @throws SivInvalidCredentialsException
     */
    public function saveStatusBrScan(array $attributes): Responseable
    {
        $this->authenticate();
        return $this->sivClient->post(sivRoutes::ENDPOINT_SAVE_STATUS_BRSCAN, $attributes);
    }

    public function queryUserSales(string $id): Responseable
    {
        $this->authenticate();
        return $this->sivClient->get(SivRoutes::ENDPOINT_SALES, ['id' => $id]);
    }

    public function rebate(array $filters = []): Responseable
    {
        $this->authenticate();
        $adapted = array_filter([
            'rede'   => preg_replace('/\-/', ' ', (string) ($filters['network'] ?? '')),
            'base'   => $filters['from'] ?? '',
            'ddd'    => $filters['areaCode'] ?? '',
            'plano'  => $filters['plan'] ?? '',
            'modelo' => $filters['model'] ?? '',
        ]);
        return $this->sivClient->get(SivRoutes::REBATE, $adapted);
    }

    public function pointOfSaleBy(array $filters = []): Responseable
    {
        $this->authenticate();
        $filters['include'] = 'network';
        return $this->sivClient->get(SivRoutes::POINT_OF_SALE_BY, $filters);
    }

    public function getPlans(string $cpf, $filters)
    {
        $this->sivClient->authenticate($cpf);
        return $this->sivClient->get(SivRoutes::LIST_PLANS, $filters);
    }

    public function contest($id, User $user): Responseable
    {
        $this->authenticate($user);
        $payload = [
            'service_form' => [
                'id' => $id
            ]
        ];
        return $this->sivClient->post(SivRoutes::CONTEST, $payload);
    }

    public function update(string $vendaId, string $serviceId, array $attributes): Responseable
    {
        $this->authenticate();

        $iccid              = data_get($attributes, 'iccid');
        $imei               = data_get($attributes, 'imei');
        $invoiceType        = data_get($attributes, 'invoiceType');
        $status             = data_get($attributes, 'status');
        $msisdn             = data_get($attributes, 'numero_acesso');
        $proposalCode       = data_get($attributes, 'codigo_proposta');
        $authorizationCode  = data_get($attributes, 'codigo_autorizacao');

        $payload['service_form'] = [
            array_filter([
                'id'                    => $serviceId,
                'iccid'                 => $iccid,
                'imei'                  => $imei,
                'tipo_fatura'           => $invoiceType,
                'status'                => $status,
                'numero_acesso'         => $msisdn,
                'codigo_proposta'       => $proposalCode,
                'codigo_autorizacao'    => $authorizationCode,
            ])
        ];

        $route = SivRoutes::ENDPOINT_SALES . "/$vendaId";
        return $this->sivClient->put($route, $payload);
    }

    public function urlResidentialFlow(): Responseable
    {
        $this->authenticate();
        $this->setBearer(Cache::get(ClaroBRCaches::USER_BEARER . Auth::user()->cpf));

        return $this->sivClient->get($this->sivRoutes::RESIDENTIAL);
    }

    public function getNegados(string $date = null): Responseable
    {
        $sentinel = config('integrations.siv.sentinel');
        if ($sentinel) {
            $this->sivClient->authenticate($sentinel);
            $payload = $date === null ? [] : ['date' => $date];
            return $this->sivClient->get(SivRoutes::ENDPOINT_NEGADOS, $payload);
        }
        throw new SivInvalidCredentialsException();
    }

    public function availableIccids(string $prefix): Responseable
    {
        $this->authenticate();
        return $this->sivClient->get(SivRoutes::ENDPOINT_ICCIDS."/$prefix");
    }

    public function updateImei(string $serviceId, array $attributes): Responseable
    {
        $this->authenticate();
        $imei = data_get($attributes, 'imei');

        throw_if(null === $imei, new AttributeNotFound($imei));

        return $this->sivClient->put(SivRoutes::ENDPOINT_UPDATE_IMEI, [
            'servico_id'   => $serviceId,
            'imei'         => $imei
        ]);
    }

    /**
     * @param int|null $serviceId
     * @throws SivInvalidCredentialsException
     */
    public function checkPayment(?int $serviceId): Responseable
    {
        $sentinel = config('integrations.siv.sentinel');
        $this->sivClient->authenticate($sentinel);

        return $this->sivClient->post(sivRoutes::ENDPOINT_CHECK_PAYMENT, [
            'servico_id' => $serviceId
        ]);
    }

    public function getResidentialPlansByCity(?string $cityId, ?string $cityIdExternal, int $attribute): Responseable
    {
        $this->authenticate();

        return $this->sivClient->post(
            sivRoutes::RESIDENTIAL_PLANS_BY_CITY,
            [
                'only_no_need_viability_plans' => $attribute,
                'cityId' => $cityId,
                'cityIdExternal' => $cityIdExternal
            ]
        );
    }
    /**
     * @param User $createdUser
     * @return Responseable
     * @throws SivInvalidCredentialsException
     */
    public function sendAutomaticRegistration(User $createdUser, array $additionalRequestData = []): Responseable
    {
        $sentinel = config('integrations.siv.sentinel');
        if ($sentinel !== null) {
            $this->sivClient->authenticate($sentinel);
            $payload = AutomaticRegistrationSendAdapter::adapt($createdUser, $additionalRequestData);
            return $this->sivClient->post(SivRoutes::SEND_AUTOMATIC_REGISTRATION, $payload);
        }
        throw new SivInvalidCredentialsException('Ocorreu uma falha na autenticação do SIV.');
    }

    /** @throws SivInvalidCredentialsException */
    public function checkAutomaticRegistrationStatus(string $protocol): Responseable
    {
        $sentinel = config('integrations.siv.sentinel');

        if ($sentinel !== null) {
            $this->sivClient->authenticate($sentinel);

            return $this->sivClient->get(SivRoutes::CHECK_AUTOMATIC_REGISTRATION_STATUS, ['cpf' => $protocol]);
        }

        throw new SivInvalidCredentialsException('Ocorreu uma falha na autenticação do SIV.');
    }
}
