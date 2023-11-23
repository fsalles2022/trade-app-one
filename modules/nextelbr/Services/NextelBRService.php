<?php

namespace NextelBR\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use NextelBR\Adapters\Request\NextelBREligibilityRequestAdapter;
use NextelBR\Assistance\OperationAssistances\NextelBRControleCartaoAssistance;
use NextelBR\Connection\NextelBR\NextelBRConnection;
use NextelBR\Enumerators\NextelBRCaches;
use NextelBR\Exceptions\EligibilityException;
use TradeAppOne\Domain\Components\Helpers\BankDataHelper;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\SaleService;

class NextelBRService
{
    const CACHE_NEXTEL_PRODUCTS = 'CACHE_NEXTEL_PRODUCTS';
    const CACHE_TIME            = 1440;

    protected $connection;
    protected $saleService;
    protected $assistance;

    public function __construct(
        NextelBRConnection $connection,
        SaleService $service,
        NextelBRControleCartaoAssistance $assistance
    ) {
        $this->connection  = $connection;
        $this->saleService = $service;
        $this->assistance  = $assistance;
    }

    public function domains(): array
    {
        $banks            = Cache::get(NextelBRCaches::BANKS);
        $dueDates         = Cache::get(NextelBRCaches::DUEDATES);
        $fromOperators    = Cache::get(NextelBRCaches::OPERATORS);
        $portabilityDates = Cache::get(NextelBRCaches::PORTABILITY_DATES);

        if (is_null($banks)) {
            $responseWithBanks = $this->connection->banks()->toArray();
            $banks             = data_get($responseWithBanks, 'bancos', []);
            if (filled($banks)) {
                Cache::put(NextelBRCaches::BANKS, $banks, NextelBRCaches::DOMAINS_DUE);
            }
        }
        if (is_null($dueDates)) {
            $responseWithDueDates = $this->connection->dueDates()->toArray();
            $dueDates             = $this->changeDueDatesAttributes(data_get(
                $responseWithDueDates,
                'datasDePagamento',
                []
            ));
            if (filled($dueDates)) {
                Cache::put(NextelBRCaches::DUEDATES, $dueDates, NextelBRCaches::DOMAINS_DUE);
            }
        }

        if (is_null($fromOperators)) {
            $responseWithOperators = $this->connection->fromOperator()->toArray();
            $fromOperators         = data_get($responseWithOperators, 'operadoras', []);
            if (filled($fromOperators)) {
                Cache::put(NextelBRCaches::OPERATORS, $fromOperators, NextelBRCaches::DOMAINS_DUE);
            }
        }

        if (is_null($portabilityDates)) {
            $responseWithPortabilityDates = $this->connection->portabilityDates()->toArray();
            $dates                        = data_get($responseWithPortabilityDates, 'datasPortabilidade', []);
            $portabilityDates             = $this->formatPortabilityDates($dates);
            if (filled($fromOperators)) {
                Cache::put(NextelBRCaches::PORTABILITY_DATES, $portabilityDates, NextelBRCaches::DOMAINS_DUE);
            }
        }

        $bankOperations = [
                [
                    'id' => '001',
                    'label' => '001 - Conta Corrente de Pessoa Física'
                ],
                [
                    'id' => '003',
                    'label' => '003 - Conta Corrente de Pessoa Jurídica'
                ],
                [
                    'id' => '023',
                    'label' => '023 - Conta Caixa Fácil'
                ]
            ];

        return compact('portabilityDates', 'banks', 'bankOperations', 'dueDates', 'fromOperators');
    }

    private function changeDueDatesAttributes(array $dueDates): array
    {
        $mapped = [];
        foreach ($dueDates as $dueDate) {
            array_push($mapped, data_get($dueDate, 'dia'));
        }

        return $mapped;
    }

    public function formatPortabilityDates($dates)
    {
        $formatedDates = [];
        foreach ($dates as $date) {
            $formated = DateConvertHelper::convertToStringFormat($date, Formats::DATE);
            array_push($formatedDates, $formated);
        }
        return $formatedDates;
    }

    public function eligibility(array $payload, User $user)
    {
        return $this->getEligibilityScore($payload, $user)->values();
    }

    private function getEligibilityScore(array $payload, User $user)
    {
        $payload['user']     = $user->cpf;
        $pointOfSale         = $user->pointsOfSale->first();
        $adapted             = NextelBREligibilityRequestAdapter::adapt($payload, $pointOfSale);
        $cpf                 = data_get($payload, 'customer.cpf');
        $eligibilityResponse = $this->connection->eligibility($adapted);
        $score               = data_get($eligibilityResponse->toArray(), 'scoreDescricao');
        if (is_null($score)) {
            throw new EligibilityException($eligibilityResponse);
        }
        $eligiblePlans = $this->getEligiblePlans($score, $payload);
        $this->cacheResults($cpf, $eligibilityResponse->toArray(), $eligiblePlans);
        return $eligiblePlans;
    }

    public function getEligiblePlans(string $eligibilityScore, array $payload = []): Collection
    {
        $areaCode      = data_get($payload, 'areaCode', '');
        $responsePlans = $this->connection->getPlans($areaCode, $eligibilityScore)->toArray();
        $plans         = data_get($responsePlans, 'planos', []);
        $operation     = data_get($payload, 'operation');
        $mode          = data_get($payload, 'mode');
        $flattenPlans  = NextelBRMapPlansService::map($plans, ['mode' => $mode, 'operation' => $operation]);

        return $flattenPlans->values();
    }

    private function cacheResults(?string $cpf, array $eligibilityResponse, Collection $eligiblePlans): void
    {
        $cache = [
            'protocolo'    => data_get($eligibilityResponse, 'protocolo'),
            'numeroPedido' => data_get($eligibilityResponse, 'numeroPedido'),
            'score'        => data_get($eligibilityResponse, 'scoreDescricao'),
            'plans'        => $eligiblePlans
        ];
        Cache::put(NextelBRCaches::ELIGIBILITY . $cpf, $cache, NextelBRCaches::ELIGIBILITY_DUE);
    }

    public function logM4uSuccess(array $payload)
    {
        $service = $this->saleService->findService(data_get($payload, 'serviceTransaction'));
        $this->saleService->pushLogService($service, $payload);
        if (data_get($payload, 'm4uResponse.action') == 'executed') {
            return $this->assistance->integrateService($service, ['executed' => true])->adapt();
        } else {
            $this->saleService->updateStatusService($service, ServiceStatus::REJECTED);
            return null;
        }
    }

    public function getProducts(array $payload = []): Collection
    {
        if ($products = Cache::get(self::CACHE_NEXTEL_PRODUCTS)) {
            return $products;
        }
        $responsePlans = $this->connection->getPlans('11', 'I')->toArray();
        $plans         = data_get($responsePlans, 'planos', []);
        $products      = NextelBRMapPlansService::map($plans, $payload);
        Cache::put(self::CACHE_NEXTEL_PRODUCTS, $products, self::CACHE_TIME);
        return $products;
    }

    public function validateBankData(array $dataRequest)
    {
        $accountRequest   = data_get($dataRequest, 'account');
        $operationRequest = data_get($dataRequest, 'operation');

        $account = BankDataHelper::composeAccount($accountRequest, $operationRequest);
        $digit   = BankDataHelper::getVerificationDigit($accountRequest);

        $bankData = [
            'id_banco'           => $dataRequest['bankId'] ?? '',
            'numero_agencia'     => $dataRequest['agency'],
            'conta_corrente'     => $account,
            'digito_verificador' => $digit
        ];

        return $this->connection->validateBankData($bankData);
    }
}
