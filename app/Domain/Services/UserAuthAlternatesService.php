<?php


namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;
use TradeAppOne\Domain\Repositories\Collections\UserAuthAlternatesRepository;

class UserAuthAlternatesService
{
    /**
     * @var UserAuthAlternatesRepository
     */
    private $repository;

    public function __construct(UserAuthAlternatesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUserAuthAlternatesRepository(string $data, User $user): UserAuthAlternates
    {
        return $this->repository->createUserAuthAlternates($data, $user);
    }

    public function updateUserAuthAlternatesRepository(string $data, User $user): UserAuthAlternates
    {
        return $this->repository->updateUserAuthAlternates($data, $user);
    }

    public function getUserAuthAlternate(?string $document): ?UserAuthAlternates
    {
        return $this->repository->findOneBy('document', $document);
    }
}
