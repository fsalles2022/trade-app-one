<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Services;

use ClaroBR\Connection\Siv3Connection;
use Illuminate\Support\Facades\App;
use SalesSimulator\Claro\Residential\Entities\Address;
use SalesSimulator\Claro\Residential\Entities\Customer;
use SalesSimulator\Claro\Residential\Exceptions\SalesSimulatorResidentialException;
use SalesSimulator\Claro\Residential\Factories\AddressFactory;
use SalesSimulator\Claro\Residential\Factories\CustomerFactory;
use TradeAppOne\Domain\Enumerators\Environments;

class SalesSimulatorViabilityService
{
    /** @var Siv3Connection */
    private $siv3Connection;

    public function __construct(Siv3Connection $siv3Connection)
    {
        $this->siv3Connection = $siv3Connection;
    }

    /** @param mixed[] $attributes */
    public function getViability(array $attributes): Address
    {
        $customer = CustomerFactory::createCustomer($attributes);

        $address = $this->getAddressWithViabilityOrWithoutViability($customer);

        return $address;
    }

    private function getAddressWithViabilityOrWithoutViability(Customer $customer): Address
    {
        $address = $this->getAddressInClaro($customer);

        if ($address->isEmptyOperatorCodeOrStateAcronym() === false) {
            return $address;
        }

        $address = $this->getFirstAddressByPostalCode($customer);

        throw_if($address->isEmptyOperatorCodeOrStateAcronym(), SalesSimulatorResidentialException::addressNotExists());

        return $address;
    }

    private function getAddressInClaro(Customer $customer): Address
    {
        $addressCollection =  collect(
            $this->siv3Connection->getAddressByPostalCode(
                substr_replace($customer->getZipCode()->getZipCode(), '-', -3, -3)
            )
                ->toArray()
        );

        return AddressFactory::create(
            $addressCollection->first()['cityIdExternal'] ?? null,
            $addressCollection->first()['operatorCode'] ?? null,
            $addressCollection->first()['stateAcronym'] ?? null,
            true
        );
    }

    private function getFirstAddressByPostalCode(Customer $customer): Address
    {
        $response = $this->siv3Connection->getAddressesByPostalCode(
            $customer->getZipCode()->getZipCode()
        );

        $address = $response->toArray()[0] ?? null;

        return AddressFactory::create(
            data_get($address, 'externalData.claroCityIdForFilterPlan'),
            data_get($address, 'externalData.claroCityIdOperatorCode'),
            data_get($address, 'state'),
            false
        );
    }
}
