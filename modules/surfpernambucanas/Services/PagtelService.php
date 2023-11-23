<?php

declare(strict_types=1);

namespace SurfPernambucanas\Services;

use Illuminate\Support\Carbon;
use SurfPernambucanas\Adapters\PagtelActivationActivateResponseAdapter;
use SurfPernambucanas\Adapters\PagtelActivationPlansResponseAdapter;
use SurfPernambucanas\Adapters\PagtelAddCardResponseAdapter;
use SurfPernambucanas\Adapters\PagtelAllocatedMsisdnResponseAdapter;
use SurfPernambucanas\Adapters\PagtelCardsResponseAdapter;
use SurfPernambucanas\Adapters\PagtelDomainsAdapter;
use SurfPernambucanas\Adapters\PagtelPlansResponseAdapter;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Adapters\PagtelUtilsAdapter;
use SurfPernambucanas\Connection\PagtelConnection;
use SurfPernambucanas\DataObjects\CreditCardDTO;
use SurfPernambucanas\Enumerators\PagtelAddressCode;
use SurfPernambucanas\Enumerators\PagtelDaysOfWeek;
use SurfPernambucanas\Enumerators\PagtelInvoiceTypes;
use SurfPernambucanas\Enumerators\PagtelPortabilityOperatorsCode;
use SurfPernambucanas\Repositories\PagtelCustomerRepository;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;

class PagtelService
{
    /** @var PagtelConnection */
    protected $connection;

    public function __construct(PagtelConnection $connection)
    {
        $this->connection = $connection;
    }

    public function subscriberActivate(string $iccid, int $areaCode, string $cpf): PagtelResponseAdapter
    {
        $response = $this->connection
            ->subscriberActivate([
                'iccid'       => $iccid,
                'areaCode'    => (string) $areaCode,
                'value'       => $cpf,
                'paymentType' => PagtelInvoiceTypes::CARTAO_CREDITO,
            ]);

        return new PagtelResponseAdapter($response);
    }

    public function allocateMsisdn(string $iccid): PagtelAllocatedMsisdnResponseAdapter
    {
        $response = $this->connection
            ->allocateMsisdn([
                'ICCID' => $iccid,
            ]);

        return new PagtelAllocatedMsisdnResponseAdapter($response);
    }

    public function plans(string $msisdn): PagtelPlansResponseAdapter
    {
        $response = $this->connection
            ->plans([
                'msisdn' => $msisdn,
            ]);

        return new PagtelPlansResponseAdapter($response);
    }

    public function getCards(string $msisdn): PagtelCardsResponseAdapter
    {
        $response = $this->connection
            ->getCards([
                'msisdn' => $msisdn,
            ]);

        return new PagtelCardsResponseAdapter($response);
    }

    public function addCard(
        string $msisdn,
        string $number,
        string $cvv,
        string $expirationMonth,
        string $expirationYear
    ): PagtelAddCardResponseAdapter {
        $response = $this->connection
            ->addCard([
                'paymentType' => PagtelInvoiceTypes::FLAGS[PagtelInvoiceTypes::CARTAO_CREDITO],
                'msisdn'      => $msisdn,
                'cardNumber'  => $number,
                'cvv'         => $cvv,
                'expiration'  => "{$expirationMonth}{$expirationYear}",
            ]);

        return new PagtelAddCardResponseAdapter($response);
    }

    public function recharge(
        string $msisdn,
        string $smsisdn,
        string $value,
        string $paymentId,
        string $cvv,
        bool $program = false
    ): PagtelResponseAdapter {
        $response = $this->connection
            ->recharge([
                'msisdn'      => $msisdn,
                'smsisdn'     => $smsisdn,
                'paymentId'   => $paymentId,
                'paymentType' => PagtelInvoiceTypes::FLAGS[PagtelInvoiceTypes::CARTAO_CREDITO],
                'value'       => $value,
                'cvv'         => $cvv,
                'program'     => $this->transformProgramBoolToString($program),
            ]);

        return new PagtelResponseAdapter($response);
    }

    public function submitPortin(
        string $msisdn,
        string $pmsisdn,
        string $cpf,
        string $operatorCode,
        string $portinDate,
        string $name
    ): PagtelResponseAdapter {
        $response = $this->connection
            ->submitPortin([
                'msisdn'      => $msisdn,
                'pmsisdn'     => MsisdnHelper::CODES[CountryAbbreviation::BR] . $pmsisdn,
                'cpf'         => $cpf,
                'operatorCode'=> $operatorCode,
                'portinDate'  => $portinDate,
                'name'        => $name
            ]);

        return new PagtelResponseAdapter($response);
    }

    public function activationPlans(): PagtelActivationPlansResponseAdapter
    {
        $response = $this->connection->activationPlans();

        return new PagtelActivationPlansResponseAdapter($response);
    }

    public function validatePlanType(string $planType, array $plans): array
    {
        return $planType === 'smartControl'
            ? $this->validateActivationPlansSmartControl($plans)
            : $this->validateActivationPlans($plans);
    }

    public function validateActivationPlans(array $plans): array
    {
        return collect($plans)->filter(function ($plan) {
            return $this->isPre($plan);
        })->values()->all();
    }

    public function isPre(array $plan): bool
    {
        return mb_strtoupper($plan['label']) == mb_strtoupper('Pré-Pago');
    }

    public function validateActivationPlansSmartControl(array $plans): array
    {
        return collect($plans)->filter(function ($plan) {
            return ! $this->isPre($plan);
        })->values()->all();
    }

    public function activationActivate(
        string $areaCode,
        string $planId,
        string $iccid,
        string $document,
        string $name,
        CreditCardDTO $card,
        bool $program = false
    ): PagtelActivationActivateResponseAdapter {
        $response = $this->connection
            ->activationActivate([
                'area_code'   => $areaCode,
                'planId'      => $planId,
                'iccid'       => $iccid,
                'document'    => $document,
                'name'        => $name,
                'recurrence'  => $program,
                'card' => [
                    'number'    => $card->getNumber(),
                    'cvv'       => $card->getCvv(),
                    'validity'  => $card->getValidity(),
                ]
            ]);

        return new PagtelActivationActivateResponseAdapter($response);
    }

    /** @return array[] */
    public function utils(): array
    {
        $utils = [
            'fromOperators' => PagtelPortabilityOperatorsCode::PORTABILITY_CODES
        ];
        return PagtelUtilsAdapter::adapt($utils);
    }

    /** @return array[] */
    public function domains(): array
    {
        $domains = [
            'local' => PagtelAddressCode::ADDRESS_TYPES
        ];
        return PagtelDomainsAdapter::adapt($domains);
    }

    /** @return mixed[] */
    public function calculatePortinDate(Carbon $date): array
    {
        $portinDate = $date->startOfDay()->addDays(2);

        if ($portinDate->isWeekend()) {
            while ($portinDate->isWeekend()) {
                $portinDate = $portinDate->addDay();
            }
        }

        return [
            'nextPortin' => [
                'date' => $portinDate->toDateString(),
                'weekday' =>  array_key_exists($portinDate->dayOfWeek, PagtelDaysOfWeek::WEEKDAY) ?
                    PagtelDaysOfWeek::WEEKDAY[$portinDate->dayOfWeek] :
                    ''
            ]
        ];
    }

    protected function transformProgramBoolToString(bool $program): string
    {
        return $program ? 'true' : 'false';
    }
}
