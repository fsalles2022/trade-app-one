<?php

namespace VivoBR\Services;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\Interfaces\UserThirdPartyRepository;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\UserRegistrationService;
use VivoBR\Adapters\Request\UserRequestAdapter;

class UserRegistrationVivoService implements UserRegistrationService
{
    protected $vivoBRUserRepository;

    public function __construct(UserThirdPartyRepository $vivoBRUserRepository)
    {
        $this->vivoBRUserRepository = $vivoBRUserRepository;
    }

    public function runOneInAPI(User $user, PointOfSale $current): array
    {
        $network = $current->network->slug;
        $adapted = UserRequestAdapter::adapt($user, $current);
        return $this->vivoBRUserRepository->createOrUpdate($adapted, $network);
    }

    public function isSyncedInAPI(User $user): bool
    {
        $pointOfSale = $user->pointsOfSale->first();
        $network     = $pointOfSale->network->slug;
        $query       = ['network' => $network, 'cpf' => $user->cpf];
        $userFromSun = $this->vivoBRUserRepository->findUser($query);
        if (filled($userFromSun)) {
            $pointsOfSaleInSun = data_get($userFromSun, 'cnpj');
            return in_array($pointOfSale->cnpj, $pointsOfSaleInSun);
        }
        return false;
    }

    public function isRegisteredInAPI(User $user): bool
    {
        $pointOfSale = $user->pointsOfSale->first();
        $network     = $pointOfSale->network->slug;
        $query       = ['network' => $network, 'cpf' => $user->cpf];
        return filled($this->vivoBRUserRepository->findUser($query));
    }

    public function getOperator(): string
    {
        return Operations::VIVO;
    }
}
