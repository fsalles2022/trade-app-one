<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use TradeAppOne\Domain\Enumerators\PasswordResetStatus;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RecoveryPermission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PasswordResetRepository;
use TradeAppOne\Exceptions\BusinessExceptions\UserAlreadyHasActiveResetRequestException;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;

class PasswordResetService extends BaseService
{
    /**
     * @var PasswordResetRepository
     */
    private $repository;

    public function __construct(PasswordResetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function registerIfNotExistPasswordResetRequest(User $user, $withManager = false): void
    {
        $existPasswordResetRequest = $this->repository->findPasswordResetRequestByUserId($user->id);
        if ($existPasswordResetRequest && ! $withManager) {
            throw new UserAlreadyHasActiveResetRequestException();
        }
        $this->repository->registerPasswordResetRequest($user);
    }

    public function filter(array $parameters): LengthAwarePaginator
    {
        return $this->repository
            ->filter($parameters)
            ->orderBy('passwordResets.createdAt', 'desc')
            ->paginate(10);
    }

    public function managerResponse(int $managerId, int $userId, bool $response)
    {
        $passwordReset = $this->repository->findPasswordResetRequestByUserId($userId);
        if (is_null($passwordReset)) {
            throw new UserNotFoundException();
        }
        $this->repository->deletePasswordResetRequest($passwordReset);
        $this->repository->updateManagerId($passwordReset, $managerId);
        if (! $response) {
            $this->repository->updateStatus($passwordReset, PasswordResetStatus::REJECTED);
            return false;
        }
        $this->userService->resetUserpassword($userId);
        $this->repository->updateStatus($passwordReset, PasswordResetStatus::APPROVED);
        return true;
    }

    public function verifyManagerPassword(string $cpf, string $password)
    {
        $user = $this->userService->findOneByCpf($cpf);

        $usersWithPermission = $this->getUsersWithPermission($user);
        $manager             = $this->repository->compareManagersPassword($usersWithPermission, $password);
        if ($manager) {
            $this->registerIfNotExistPasswordResetRequest($user, true);
            $this->managerResponse($manager->id, $user->id, true);
            return true;
        }
    }

    public function verifyUserPassword(string $cpf, string $password)
    {
        $user = $this->userService->findOneByCpf($cpf);
        return password_verify($password, $user->password);
    }

    public function getUsersWithPermission(User $user)
    {
        $pointsOfSaleIds        = $user->pointsOfSale()->get()->pluck('id');
        $allUsersOfPointsOfSale = $this->userService
            ->findManyByPointOfSale($pointsOfSaleIds->toArray());
        $usersWithPermission    = $this->userService
            ->findByPermission($allUsersOfPointsOfSale, RecoveryPermission::getFullName(PermissionActions::APPROVE));
        return $usersWithPermission;
    }

    public function sendRequestToManager(string $cpf)
    {
        $user = $this->userService->findOneByCpf($cpf);
        if ($user && ! $user->isInactive()) {
            $this->registerIfNotExistPasswordResetRequest($user);
            return true;
        }
    }
}
