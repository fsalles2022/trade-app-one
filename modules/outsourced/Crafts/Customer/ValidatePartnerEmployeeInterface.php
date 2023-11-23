<?php

declare(strict_types=1);

namespace Outsourced\Crafts\Customer;

interface ValidatePartnerEmployeeInterface
{
    /** @return mixed[] */
    public function validatePartnerEmployee(string $cpf): array;
}
