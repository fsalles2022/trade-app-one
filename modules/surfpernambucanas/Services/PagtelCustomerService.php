<?php

declare(strict_types=1);

namespace SurfPernambucanas\Services;

use Outsourced\Crafts\Customer\ValidatePartnerEmployeeInterface;
use SurfPernambucanas\Repositories\PagtelCustomerRepository;

class PagtelCustomerService implements ValidatePartnerEmployeeInterface
{
    /** @var PagtelCustomerRepository */
    private $pagtelCustomerRepository;

    public function __construct(PagtelCustomerRepository $pagtelCustomerRepository)
    {
        $this->pagtelCustomerRepository = $pagtelCustomerRepository;
    }

    /** @return mixed[] */
    public function validatePartnerEmployee(string $cpf): array
    {
        return $this->pagtelCustomerRepository->validate($cpf)->partner()->sale()->get();
    }
}
