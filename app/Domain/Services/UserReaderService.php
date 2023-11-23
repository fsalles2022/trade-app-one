<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Exportables\UserExport;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Policies\Authorizations;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;

class UserReaderService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    private $authorizations;

    public function __construct(UserRepository $userRepository, Authorizations $authorizations)
    {
        $this->userRepository = $userRepository;
        $this->authorizations = $authorizations;
    }

    /** @param mixed[] $filters */
    public function exportUsers(array $filters = []): UserExport
    {
        $export = new UserExport();
        $query  = $this->resumeToExport($filters);

        $query->chunk(10000, function ($users) use (&$export) {
            $export->processCollection($users);
        });

        return $export;
    }

    /** @param mixed[] $parameters */
    private function resumeToExport(array $parameters): Builder
    {
        return $this->userRepository->filter($parameters)
            ->without('hierarchies')
            ->with('role:id,slug,name,networkId', 'pointsOfSale:slug,cnpj', 'passwordResets', 'userAuthAlternate:document,userId')
            ->select('id', 'firstName', 'lastName', 'cpf', 'birthday', 'activationStatusCode', 'lastSignin', 'roleId');
    }

    public function pointOfSaleWithUser(User $user): Builder
    {
        return PointOfSaleRepository::findByServiceOptions(ServiceOption::CARTEIRIZACAO, $user)->with('users');
    }
}
