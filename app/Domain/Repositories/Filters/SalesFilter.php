<?php

namespace TradeAppOne\Domain\Repositories\Filters;

use Carbon\Carbon;
use MongoDB\BSON\Regex;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;

class SalesFilter
{
    protected $contextFilter;
    protected $filters;

    public function __construct(array $contextFilter = [])
    {
        $this->contextFilter = $contextFilter;
        $this->filters       = [];
    }

    public function build(array $filters): array
    {
        foreach ($filters as $filter => $value) {
            $this->{$filter}($value);
        }

        return array_merge($this->contextFilter, $this->filters);
    }

    private function name($value)
    {
        $firstName = ['services.customer.firstName' => (new Regex("$value"))];
        $lastName  = ['services.customer.lastName'  => (new Regex("$value"))];

        $this->setExistingKey('$or', [$firstName, $lastName]);
    }

    private function cpfSalesman($value)
    {
        $this->filters += ['user.cpf' => (new Regex("$value"))];
    }

    private function cpfCustomer($value)
    {
        $this->filters += ['services.customer.cpf' => (new Regex("$value"))];
    }

    private function cpf($value)
    {
        $cpfSalesman = ['user.cpf'               => (new Regex("$value"))];
        $cpfCustomer = ['services.customer.cpf'  => (new Regex("$value"))];

        $this->setExistingKey('$or', [$cpfSalesman, $cpfCustomer]);
    }

    private function saleTransaction($value)
    {
        $this->filters += ['saleTransaction' => $value];
    }

    private function saleId($value)
    {
        $this->filters += ['saleTransaction' => (new Regex("$value"))];
    }

    private function operator($values)
    {
        $in             = ['$in' => array_wrap($values)];
        $this->filters += ['services.operator' => $in];
    }

    private function imei($value)
    {
        $this->filters += ['services.imei' => $value];
    }

    private function log($value)
    {
        $keysInteger = [
            'services.operatorIdentifiers.servico_id',
            'services.operatorIdentifiers.venda_id'
        ];

        $keysString = [
            'services.log.type',
            'services.log.message'
        ];

        $conditions = [];

        foreach ($keysInteger as $key) {
            if (is_numeric($value)) {
                array_push($conditions, [$key => intval($value)]);
            }
        }

        foreach ($keysString as $string) {
            array_push($conditions, [$string => (new Regex($value))]);
        }

        $this->setExistingKey('$or', $conditions);
    }

    private function pointsOfSale(array $values)
    {
        $in             = ['$in' => $values];
        $this->filters += ['pointOfSale.cnpj' => $in];
    }

    private function startDate($value)
    {
        $startDate = Carbon::parse($value);
        $gte       = ['$gte' => MongoDateHelper::dateTimeToUtc($startDate)];

        $this->setExistingKey('createdAt', $gte);
    }

    private function endDate($value)
    {
        $endDay = Carbon::parse($value);
        $lt     = ['$lt'  => MongoDateHelper::dateTimeToUtc($endDay)];

        $this->setExistingKey('createdAt', $lt);
    }

    private function status($values)
    {
        $in             = ['$in' => array_wrap($values)];
        $this->filters += ['services.status' => $in];
    }

    private function operation($value)
    {
        $this->filters += ['services.operation' => (new Regex(strtoupper($value)))];
    }

    private function mode($value)
    {
        $this->filters += ['services.mode' => (new Regex(strtoupper($value)))];
    }

    private function ntc($value): void
    {
        $this->filters += ['$or' => [
            ['services.msisdn' => (new Regex(($value)))],
            ['services.portedNumber'     => (new Regex(($value)))],
            ['services.log.numeroAcesso' => (new Regex(($value)))]
        ]
        ];
    }

    private function networks(array $values)
    {
        $in             = ['$in' => $values];
        $this->filters += ['pointOfSale.network.slug' => $in];
    }

    private function tradeHubCheckoutProductItemId(?string $value): void
    {
        $this->setExistingKey('services.tradeHub.checkoutProductItemId', $value);
    }

    private function setExistingKey($key, $value)
    {
        if (array_key_exists($key, $this->filters)) {
            $this->filters[$key] = array_merge($this->filters[$key], $value);
        } else {
            $this->filters += [$key => $value];
        }
    }

    /** @param string[] $values */
    private function operators(array $values): void
    {
        $this->filters += [
            'services.operator' => [
                '$in' => $values
            ]
        ];
    }

    /** @param string[] $values */
    private function operations(array $values): void
    {
        $this->filters += [
            'services.operation' => [
                '$in' => $values
            ]
        ];
    }

    /** @param string[] $values */
    private function hierarchies(array $values): void
    {
        $this->filters += [
            'pointOfSale.hierarchy.slug' => [
                '$in' => $values
            ]
        ];
    }

    private function sentToTimCommissioning(?bool $value): void
    {
        if ($value === null) {
            $this->filters += [
                'services.sentToTimCommissioning' => [
                    '$exists' => false
                ]
            ];

            return;
        }

        $this->filters += ['services.sentToTimCommissioning' => $value];
    }
}
