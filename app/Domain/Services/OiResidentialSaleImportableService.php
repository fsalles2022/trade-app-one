<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Builder\OiResidentialSaleBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\OiResidentialSaleImportableExceptions;

use DateTime;

class OiResidentialSaleImportableService
{
    /** @var UserRepository */
    private $userRepository;

    /** @var SaleRepository */
    private $saleRepository;

    /** @var PointOfSaleRepository */
    private $pointOfSaleRepository;

    /** @var User */
    private $user;

    /** @var PointOfSale */
    private $pointOfSale;

    /** @var mixed[] */
    private $customer;

    /** @var mixed[] */
    private $plan;

    /** @var DateTime */
    private $createdAt;

    public function __construct(
        SaleRepository $saleRepository,
        UserRepository $userRepository,
        PointOfSaleRepository $pointOfSaleRepository
    ) {
        $this->saleRepository        = $saleRepository;
        $this->userRepository        = $userRepository;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
    }

    /** @param mixed[] $line */
    public function validateIfSaleExists(array $line): void
    {
        $sale = $this->saleRepository->getByFilters([
            'salesmanCpf'   => $line['salesmanCpf'] ?? null,
            'operator'      => Operations::OI,
            'operation'     => Operations::OI_RESIDENCIAL,
            'customerCpf'   => $line['customerCpf'] ?? null,
            'serviceName'   => $line['plan'] ?? null,
            'servicePrice'  => (float) $line['valuePlan'] ?? 0,
            'createdAt'     => (new DateTime(str_replace('/', '-', $line['createdAt']) ?? 'now'))
        ])->first();

        throw_if($sale instanceof Sale, OiResidentialSaleImportableExceptions::saleAlreadyExists());
    }

    /** @param mixed[] $line */
    public function adaptedData(array $line): void
    {
        $this->setUser($line);
        $this->setCustomer($line);
        $this->setPlan($line);
        $this->setPdv($line);
        $this->setCreatedAt($line);
    }

    public function saveOiResidentialSale(): Sale
    {
        return $this->saleRepository->save(
            (new OiResidentialSaleBuilder())
                ->buildCustomer($this->getCustomer())
                ->buildPointOfSale($this->getPointOfSale())
                ->buildService($this->getPlan())
                ->buildUser($this->getUser())
                ->buildSale($this->getCreatedAt())
                ->build()
        );
    }

    /** @param mixed[] $lines */
    private function setUser(array $lines): void
    {
        $user = $this->userRepository->findOneBy('cpf', $lines['salesmanCpf'] ?? null);
        throw_unless($user instanceof User, OiResidentialSaleImportableExceptions::salesmanNotFound());

        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /** @param mixed[] $lines */
    private function setCustomer(array $lines): void
    {
        $this->customer = array_merge(
            [
                'cpf'           => $lines['customerCpf'] ?? null,
                'birthday'      => $lines['customerBirthday'] ?? null,
                'local'         => $lines['customerAddress'] ?? null,
                'complement'    => $lines['customerAddressComplement'] ?? null,
                'number'        => $lines['customerAddressNumber'] ?? null,
                'city'          => $lines['customerCity'] ?? null,
                'state'         => $lines['customerState'] ?? null,
                'zipCode'       => $lines['customerZipCode'] ?? null
            ],
            $this->adaptName($lines['customerName'])
        );
    }

    /** @return mixed[] */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /** @param mixed[] $lines */
    private function setPlan(array $lines): void
    {
        $this->plan = [
            'name' => $lines['plan'],
            'value' => $lines['valuePlan']
        ];
    }

    /** @return mixed[] */
    public function getPlan(): array
    {
        return $this->plan;
    }

    /** @param mixed[] $lines */
    private function setPdv(array $lines): void
    {
        $pointOfSale = $this->pointOfSaleRepository->findOneBy('cnpj', $lines['cnpj'] ?? null);
        throw_unless($pointOfSale instanceof PointOfSale, PointOfSaleNotFoundException::class);

        $this->pointOfSale = $pointOfSale;
    }

    public function getPointOfSale(): ?PointOfSale
    {
        return $this->pointOfSale;
    }

    /** @param mixed[] $line */
    private function setCreatedAt(array $line): void
    {
        $date            = str_replace('/', '-', $line['createdAt']);
        $this->createdAt = new DateTime($date ?? 'now');
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /** @return mixed[] */
    private function adaptName(?string $name): array
    {
        if (is_null($name)) {
            return ['firstName' => null, 'lastName' => null];
        }

        $parts = explode(" ", $name);
        return ['firstName' => array_shift($parts), 'lastName' => implode(" ", $parts)];
    }
}
