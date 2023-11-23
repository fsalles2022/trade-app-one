<?php

namespace ClaroBR\Services;

use ClaroBR\Adapters\ClaroBrMapPlansMapper;
use ClaroBR\Adapters\ClaroBrUtilsMapper;
use ClaroBR\Adapters\CreditAnalysisResponseAdapter;
use ClaroBR\Connection\SivConnection;
use ClaroBR\Enumerators\ClaroBRCaches;
use ClaroBR\Enumerators\ClaroRebate;
use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Exceptions\PlansNotFoundException;
use ClaroBR\Exceptions\SivInvalidCredentialsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Throwable;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Features\Customer\Adapter\CustomerNotNested;

class SivService
{
    protected $connection;
    protected $sivConnection;
    protected $saleRepository;

    public function __construct(SivConnection $sivConnection, SaleRepository $saleRepository)
    {
        $this->sivConnection  = $sivConnection;
        $this->saleRepository = $saleRepository;
    }

    public function saveCredentials($cpf, $password): bool
    {
        $userSiv = $this->sivConnection->getUserSiv($cpf, $password);
        if ($userSiv) {
            $password            = Crypt::encryptString($password);
            $userSiv['password'] = $password;

            $user = Auth::user();
            $user->update(['integrationCredentials' => ['siv' => $userSiv]]);

            return true;
        }
        return false;
    }

    public function creditAnalysis(array $data = []): JsonResponse
    {
        event(new PreAnalysisEvent(new CustomerNotNested($data)));

        $response = $this->sivConnection->creditAnalysis($data);
        $adapter  = new CreditAnalysisResponseAdapter($response);
        return $adapter->adapt();
    }

    public function residentialFlow(): array
    {
        return $this->sivConnection->urlResidentialFlow()->toArray();
    }

    public function analiseAuthenticate(array $attributes): Responseable
    {
        $payload  = $this->authenticateCommonRequeriments($attributes);
        $response = $this->sivConnection->analiseAuthenticate($payload);

        $this->checkBRScanResponse($response);

        return $response;
    }

    public function utilsForCreateSale(array $filter = [])
    {
        if ($collection = Cache::get(ClaroBRCaches::CLARO_UTILS)) {
            return $collection->pluck($filter)->first() ?? $collection->first();
        }
        $utils = $this->sivConnection->utils()->toArray();

        $translatedUtils['invoiceType']   = $utils['tipo_faturas'] ?? [];
        $translatedUtils['banks']         = $utils['bancos'] ?? [];
        $translatedUtils['professions']   = $utils['profissoes'] ?? [];
        $translatedUtils['dueDate']       = $utils['vencimentos'] ?? [];
        $translatedUtils['local']         = $utils['logradouros'] ?? [];
        $translatedUtils['salaryRange']   = $utils['faixas_salariais'] ?? [];
        $translatedUtils['areaCode']      = $utils['ddd'] ?? [];
        $translatedUtils['maritalStatus'] = $utils['estados_civis'] ?? [];
        $collection                       = collect([$translatedUtils]);

        Cache::remember(
            ClaroBRCaches::CLARO_UTILS,
            ClaroBRCaches::UTILS_DUE,
            static function () use ($collection) {
                return $collection;
            }
        );

        return $collection->pluck($filter)->first() ?? $collection->first();
    }

    public function plans(array $filters = []): array
    {
        $result = $this->sivConnection->plans($filters)->toArray();
        $plans  = data_get($result, 'data.data');

        if (empty($plans)) {
            throw new PlansNotFoundException();
        }

        return $result;
    }

    public function domains()
    {
        if ($cachedUtils = Cache::get(ClaroBRCaches::CLARO_DOMAINS)) {
            return $cachedUtils;
        }

        $utils       = $this->sivConnection->utils()->toArray();
        $utilsMapped = ClaroBrUtilsMapper::map($utils);

        Cache::put(ClaroBRCaches::CLARO_DOMAINS, $utilsMapped, ClaroBRCaches::UTILS_DUE);

        return $utilsMapped;
    }

    public function contest(int $serviceId, int $userId): array
    {
        $user = User::find($userId);
        throw_if(! $user instanceof User, new UserNotFoundException());
        $response = $this->sivConnection->contest($serviceId, $user)->toArray();
        $type     = data_get($response, 'type');
        $status   = data_get($response, 'status');
        $message  = data_get($response, 'message', '');

        if ($type === 'error') {
            throw ClaroExceptions::CONTEST_UNAVAILABLE($message);
        }

        if ($type === 'success' && filled($status)) {
            return $response;
        }

        throw ClaroExceptions::CONTEST_INVALID_RESPONSE();
    }

    public function getSale($identifiers): Collection
    {
        $responseWithSale = $this->sivConnection->querySales(['id' => $identifiers['venda_id']])->toArray();
        return collect(data_get($responseWithSale, 'data.data', []));
    }

    public function devices(User $user): Collection
    {
        if ($user->role) {
            $response = $this->sivConnection->rebate(['network' => strtoupper($user->getNetwork()->slug)]);
            $devices  = data_get($response->toArray(), 'data.rebate', []);
            return collect($devices)->map(
                static function ($device) {
                    return ['slug' => data_get($device, 'sanitized'), 'label' => data_get($device, 'model')];
                }
            );
        }
        return collect();
    }

    public function rebateDevices($deviceSivSlug, User $user): Collection
    {
        $userNetwork = $user->getNetwork()->slug;

        $networksRebateOperation = [
            Operations::CLARO_POS,
            Operations::CLARO_CONTROLE_BOLETO
        ];

        if (array_key_exists($userNetwork, ClaroBRDiscountService::NETWORK_SHOULD_USE_REBATE)) {
            $networksRebateOperation = ClaroBRDiscountService::NETWORK_SHOULD_USE_REBATE[$userNetwork];
        }

        $filtered = collect();
        $products = $this->products(
            [
                'areaCode' => 11,
                'operation' => $networksRebateOperation
            ]
        )->unique('label');

        foreach ($products as $product) {
            try {
                $rebate           = $this->rebate(
                    [
                        'network' => $userNetwork,
                        'base' => ClaroRebate::ACTIVATION,
                        'model' => $deviceSivSlug,
                        'plan' => $product->original['nome']
                    ],
                    $user
                );
                $rebate           = data_get($rebate, 'data.rebate');
                $productOperation = data_get($product->original, 'plan_type.nome');
                $formatRebate     = [
                    'priceWith' => data_get($rebate, 'valor_plano'),
                    'priceWithout' => data_get($rebate, 'valor_pre'),
                    'penalty' => data_get($rebate, 'multa')
                ];
                $rebateItem       = [
                    'label' => $product->label,
                    'price' => $product->price,
                    'operation' => $productOperation,
                    'rebate' => $formatRebate
                ];

                $filtered->push($rebateItem);
            } catch (BuildExceptions $exception) {
                continue;
            }
        }

        return $filtered->sortByDesc('price')->values();
    }

    public function products(array $filters = [], ?User $user = null): Collection
    {
        $areaCode          = data_get($filters, 'areaCode', null);
        $query             = $areaCode ? ['ddd' => $areaCode] : [];
        $result            = $this->sivConnection->plans($query, $user)->toArray();
        $plansFromOperator = data_get($result, 'data.data', []);
        $productsMapped    = ClaroBrMapPlansMapper::map($plansFromOperator);

        return ClaroBrMapPlansFilter::filter($productsMapped, $filters);
    }

    public function rebate(array $filters, User $user): array
    {
        $hasNetwork = data_get($filters, 'network', false);

        if ($hasNetwork) {
            $filters['network'] = strtoupper($user->getNetwork()->slug);
        }

        $rebate                             = $this->sivConnection->rebate($filters)->toArray();
        $hasValueIndicatingInvalidStructure = data_get($rebate, 'data.rebate.mobile_operators');

        if ($hasValueIndicatingInvalidStructure) {
            throw ClaroExceptions::REBATE_WITH_INVALID_STRUCTURE();
        }

        return $rebate;
    }

    public function userLines(string $cpf): array
    {
        $response = $this->sivConnection->creditAnalysis(['cpf' => $cpf]);

        return collect($response->get('data.products', []))
            ->where('type', '=', 'PRE_PAGO')
            ->where('status', '=', 'ATIVO')
            ->values()
            ->toArray();
    }

    public function availableIccids(string $prefix): array
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->isInovaPromoter()) {
            return [
                'body' => ['messages' => trans('siv::messages.iccid.not_promoter')],
                'code' => Response::HTTP_PRECONDITION_FAILED
            ];
        }

        if (preg_match('/\d{6,}/', $prefix) === 0) {
            return [
                'body' => ['messages' => trans('siv::messages.iccid.min_length')],
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }

        $response           = $this->sivConnection->availableIccids($prefix);
        $responseCollection = collect($response->toArray());
        return $response->isSuccess() ? [
            'body' => data_get($responseCollection, 'data'),
            'code' => Response::HTTP_OK
        ] : [
            'body' => data_get($responseCollection, 'messageForHumans', trans('messages.default')),
            'code' => data_get($responseCollection, 'httpCode', Response::HTTP_BAD_REQUEST)
        ];
    }

    public function statusAuthenticate(array $attributes): Responseable
    {
        $payload  = $this->authenticateCommonRequeriments($attributes);
        $response = $this->sivConnection->statusAuthenticate($payload);

        $this->checkBRScanResponse($response);

        return $response;
    }

    /**
     * @param mixed[] $attributes
     * @return Responseable
     * @throws SivInvalidCredentialsException
     * @throws Throwable
     * @throws SaleNotFoundException
     */
    public function saveStatus(array $attributes): Responseable
    {
        $service             = $this->saleRepository->findInSale(data_get($attributes, 'serviceTransaction', ''));
        $payload             = $this->authenticateCommonRequeriments($attributes);
        $payload['venda_id'] = data_get($service->operatorIdentifiers, 'venda_id', '');

        throw_unless(isset($payload['venda_id']), new SaleNotFoundException());

        return $this->sivConnection->saveStatusBrScan($payload);
    }

    private function checkBRScanResponse(Responseable $response)
    {
        $exceptionTypesToMessageForHumans = [
            "BRScanCustomerCpfAnalysisNotFound",
            "BRScanCpfFraudRisk"
        ];

        if ($response->getStatus() !== Response::HTTP_OK) {
            $message = in_array($response->get('type'), $exceptionTypesToMessageForHumans) ?
                $response->get('messageForHumans') :
                $response->get('reason');
            throw ClaroExceptions::brScanInvalidResponse($message);
        }
    }

    private function authenticateCommonRequeriments(array $attributes): array
    {
        $userPointOfSale            = Auth::user()->pointsOfSale()->first();
        $operatorProviderIdentifier = data_get($userPointOfSale, 'providerIdentifiers.' . Operations::CLARO, null);

        throw_if(null === $operatorProviderIdentifier, ClaroExceptions::authenticateWithoutPointOfSaleCode());

        return [
            'customer_cpf' => data_get($attributes, 'cpf', ''),
            'pdv_code' => $operatorProviderIdentifier
        ];
    }
}
