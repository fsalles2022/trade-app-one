<?php

namespace ClaroBR\Connection;

use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Tables\User;

interface SivConnectionInterface
{
    public function plans(array $filters = [], ?User $user = null): Responseable;

    public function utils(): Responseable;

    public function authenticate(?User $user = null);

    public function sale(array $customer, array $services, $extra = null): Responseable;

    public function logSale($payload, $sale): Responseable;

    public function activate(string $servicoId, ?string $msisdn, array $extraPayload): Responseable;

    public function getUserSiv($cpf, $password);

    public function rebate(array $filters = []): Responseable;

    public function getIdentifiers(string $route, string $cpf): Responseable;

    public function pointOfSaleBy(array $filters = []): Responseable;

    public function update(string $vendaId, string $serviceId, array $payload): Responseable;

    public function getNegados(string $date = null);

    public function availableIccids(string $cpf);

    public function getResidentialPlansByCity(?string $cityId, ?string $cityIdExternal, int $attribute): Responseable;
}
