<?php

declare(strict_types=1);

namespace ClaroBR\Services;

use ClaroBR\Adapters\Siv3ResidencialAddressToHpAddress;
use ClaroBR\Connection\Siv3Connection;
use ClaroBR\Enumerators\Siv3Proposal;
use ClaroBR\Enumerators\Siv3Viability;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Domain\Services\SaleService;

class ViabilityService extends BaseService
{
    private const RESIDENTIAL_TYPE = 'residential';
    private const CLARO_BOX_TYPE   = 'clarobox';

    /** @var Siv3Connection */
    private $siv3Connection;

    /** @var SaleService */
    private $saleService;

    public function __construct(Siv3Connection $siv3Connection, SaleService $saleService)
    {
        $this->siv3Connection = $siv3Connection;
        $this->saleService    = $saleService;
    }

    /**
     * @return mixed[]
     */
    public function getViability(string $serviceTransaction): array
    {
        $service  = $this->saleService->findService($serviceTransaction);
        $user     = $service->sale->user;
        $customer = $service->customer;
        $zipCode  = $customer['zipCode'] ?? null;
        $birthday = $customer['birthday'] ?? null;

        if (empty($zipCode) === true || empty($birthday) === true) {
            return [
                'viability' => [
                    'type' => null,
                    'status' => false,
                    'proposalId' => null
                ],
            ];
        }

        $addressCollection      = $this->getAddressByPostalCode($zipCode);
        $creditAnalysisResponse = $this->getCreditAnalysisResponse($user, $customer, $addressCollection);

        /** Return false to empty creditAnalysis */
        if ($creditAnalysisResponse === null) {
            return [
                'viability' => [
                    'type' => null,
                    'status' => false,
                    'proposalId' => null
                ],
            ];
        }

        $proposal = $this->postResidentialProposal(
            $service,
            Siv3ResidencialAddressToHpAddress::adapt($addressCollection->first()),
            $creditAnalysisResponse->toArray()
        );

        return [
            'viability' => [
                'type'   => $this->getTypeByAddresses($addressCollection),
                'status' => $this->viabilityStatusValidate($creditAnalysisResponse),
                'proposalId' => $proposal->get('id')
            ]
        ];
    }

    private function getAddressByPostalCode(string $zipCode): Collection
    {
        return collect($this->siv3Connection
            ->getAddressByPostalCode(substr_replace($zipCode, '-', -3, -3))
            ->toArray());
    }

    /**
     * @param mixed[] $customer
     * @param mixed[] $seller
     */
    private function getCreditAnalysisResponse(array $seller, array $customer, Collection $addressCollection): ?Responseable
    {
        $operatorCode      = $addressCollection->first()['operatorCode'] ?? null;
        $stateAcronym      = $addressCollection->first()['stateAcronym'] ?? null;
        $residentialCityId = $addressCollection->first()['cityIdExternal'] ?? null;
        $customerZipCode   = $customer['zipCode'] ?? null;

        /** return when zipCode customer is empty */
        if (empty($customerZipCode)) {
            return null;
        }

        if ($this->isEmptyOperatorCodeOrStateAcronym($operatorCode, $stateAcronym)) {
            /** Get address from SVI3 when zipCode doesn't have viability address */
            $address = $this->getFirstAddressByPostalCode((string) $customerZipCode);

            $operatorCode      = data_get($address, 'externalData.claroCityIdOperatorCode');
            $residentialCityId = data_get($address, 'externalData.claroCityIdForFilterPlan');
            $stateAcronym      = data_get($address, 'state');

            if ($this->isEmptyOperatorCodeOrStateAcronym($operatorCode, $stateAcronym)) {
                return null;
            }
        }

        return $this->siv3Connection->getResidentialCreditAnalysis([
            'company'           => Siv3Viability::VIABILITY_COMPANY,
            'name'              => trim(($customer['firstName'] ?? '') . ' ' . ($customer['lastName'] ?? '')),
            'birthdate'         => $customer['birthday'] ?? '',
            'cpf'               => $customer['cpf'] ?? '',
            'cityId'            => (string) $operatorCode,
            'stateAcronym'      => $stateAcronym,
            'postalCode'        => $customerZipCode,
            'seller'            => "{$seller['firstName']} {$seller['lastName']}",
            'residentialCityId' => $residentialCityId,
        ]);
    }

    /**
     * @param mixed[]|null $tecnicalViability
     * @param mixed[] $creditAnalysis
     */
    private function postResidentialProposal(Service $service, ?array $tecnicalViability, array $creditAnalysis): Responseable
    {
        return $this->siv3Connection->postResidentialProposal([
            'origin' => Siv3Proposal::getEnumByOperation($service->operation),
            'salesmanCpf' => $service->sale->user['cpf'],
            'technicalViabilityHp' => $tecnicalViability,
            'creditAnalysis' => $creditAnalysis,
        ]);
    }

    private function viabilityStatusValidate($creditAnalysisResponse): bool
    {
        return ! (
            data_get($creditAnalysisResponse->toArray(), 'systemStatus') !== strtolower(ServiceStatus::APPROVED)
        );
    }

    /** @return mixed[]|null */
    private function getFirstAddressByPostalCode(string $postalCode): ?array
    {
        $response = $this->getAddressesByPostalCode($postalCode);

        return $response->toArray()[0] ?? null;
    }

    public function getAddressesByPostalCode(string $postalCode): ?Responseable
    {
        return $this->siv3Connection->getAddressesByPostalCode($postalCode);
    }

    /**
     * @param mixed $operatorCode
     * @param mixed $stateAcronym
     * @return bool
     */
    private function isEmptyOperatorCodeOrStateAcronym($operatorCode, $stateAcronym): bool
    {
        return empty($operatorCode) || empty($stateAcronym);
    }

    private function getTypeByAddresses(Collection $addresses): string
    {
        $operatorCode = $addresses->first()['operatorCode'] ?? null;
        $stateAcronym = $addresses->first()['stateAcronym'] ?? null;

        if ($this->isEmptyOperatorCodeOrStateAcronym($operatorCode, $stateAcronym)) {
            return self::CLARO_BOX_TYPE;
        }

        return self::RESIDENTIAL_TYPE;
    }
}
