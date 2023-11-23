<?php

namespace TradeAppOne\Domain\Services;

use ClaroBR\Exceptions\SivAutomaticRegistrationExceptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\ContextHelper;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;
use TradeAppOne\Domain\Repositories\Collections\UserWriteRepository;
use TradeAppOne\Domain\Repositories\Tables\AccessLogRepository;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use Illuminate\Support\Facades\Mail;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Facades\SyncUserOperators;
use TradeAppOne\Mail\UserPasswordChangedMail;
use TradeAppOne\Notifications\NotifyUser;

class UserService extends BaseService
{
    /** @var UserRepository */
    private $repository;

    /** @var AccessLogRepository */
    protected $accessLogRepository;

    /** @var NotifyUser */
    private $notifyUser;

    public function __construct(
        AccessLogRepository $accessLogRepository,
        UserRepository $repository,
        NotifyUser $notifyUser
    ) {
        $this->accessLogRepository = $accessLogRepository;
        $this->repository          = $repository;
        $this->notifyUser          = $notifyUser;
    }

    /** @param mixed[] $data */
    public function createUser(array $data = []): User
    {
        $roleSlug        = data_get($data, 'role');
        $pointOfSaleCnpj = data_get($data, 'pointOfSale');
        $hierarchySlug   = data_get($data, 'hierarchy');
        $matriculation   = data_get($data, 'matriculation');

        $hashedPassword = $this->generatePassword(7, false, true, true, true);

        $data['activationStatusCode'] = UserStatus::NON_VERIFIED;
        $data['password']             = bcrypt($hashedPassword);

        $role = $this->roleService->findOneBySlug($roleSlug);
        $user = UserWriteRepository::createUser($data, $role);
        Mail::send(new UserPasswordChangedMail($user, $hashedPassword, false));

        $this->generateAndRegisterVerificationCode($user);

        if ($pointOfSaleCnpj) {
            $pointOfSale = $this->pointOfSaleService->findOneByCnpj($pointOfSaleCnpj);
            $user->pointsOfSale()->sync([$pointOfSale->id]);
            SyncUserOperators::sync($user, $pointOfSale);
        }

        if ($hierarchySlug) {
            $hierarchy = $this->hierarchyService->findOneHierarchyBySlug($hierarchySlug);
            $user->hierarchies()->sync([$hierarchy->id]);
        }

        if ($matriculation) {
            $this->userAuthAlternatesService->createUserAuthAlternatesRepository($matriculation, $user);
        }

        return $user;
    }

    private function generatePassword(
        int $length = 7,
        bool $upper = true,
        bool $lower = true,
        bool $numbers = true,
        bool $symbols = true
    ) : string {
        $ma       = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
        $mi       = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
        $nu       = "0123456789"; // $nu contem os números
        $si       = "!@#"; // $si contem os símbolos
        $password = "";
        if ($upper === true) {
            $password .= str_shuffle($ma);
        }
        if ($lower === true) {
            $password .= str_shuffle($mi);
        }
        if ($numbers === true) {
            $password .= str_shuffle($nu);
        }
        if ($symbols === true) {
            $password .= str_shuffle($si);
        }
        return substr(str_shuffle($password), 0, $length);
    }

    /**
     * @param PointOfSale $pointOfSale
     * @param Hierarchy $hierarchy
     * @param Role $role
     * @param mixed[] $attributes
     * @return mixed[]
     */
    public function createUserWithAutomaticRegistration(PointOfSale $pointOfSale, Hierarchy $hierarchy, Role $role, array $attributes): array
    {
        $attributes['activationStatusCode'] = UserStatus::NON_VERIFIED;

        $hashedPassword = $this->generatePassword(7, false, true, true, true);

        $attributes['password'] = bcrypt($hashedPassword);

        $user = UserWriteRepository::createUser($attributes, $role);

        $user->pointsOfSale()->sync([$pointOfSale->id]);
        $user->hierarchies()->sync([$hierarchy->id]);

        $matriculation = data_get($attributes, 'matriculation');

        if ($matriculation) {
            $this->userAuthAlternatesService->createUserAuthAlternatesRepository($matriculation, $user);
        }

        return [
            'user' => $user,
            'hashedPassword' => $hashedPassword
        ];
    }

    public function generateAndRegisterVerificationCode(User $user)
    {
        $verificationCode = str_random(30);
        $this->repository->registerVerificationCode($verificationCode, $user);

        return $verificationCode;
    }

    public function verifyTokenGenerated(User $user)
    {
        return $this->repository->checkAlreadyHasToken($user);
    }

    public function filter(array $parameters): LengthAwarePaginator
    {
        return $this->repository->filterAndPaginate($parameters, 10);
    }

    public function updateUser(User $user, array $data = []): User
    {
        $roleSlug        = data_get($data, 'role');
        $pointOfSaleCnpj = data_get($data, 'pointOfSale');
        $hierarchySlug   = data_get($data, 'hierarchy');
        $matriculation   = data_get($data, 'matriculation');

        $role = $this->roleService->findOneBySlug($roleSlug);
        $user->role()->associate($role);

        if ($pointOfSaleCnpj) {
            $pointOfSaleToAdd = $this->pointOfSaleService->findOneByCnpj($pointOfSaleCnpj);
            $changes          = $user->pointsOfSale()->sync([$pointOfSaleToAdd->id]);
            SyncUserOperators::sync($user, $pointOfSaleToAdd, $changes);
        }

        if ($hierarchySlug) {
            $hierarchy = $this->hierarchyService->findOneHierarchyBySlug($hierarchySlug);
            $user->hierarchies()->sync([$hierarchy->id]);
        }

        if ($matriculation) {
            $this->userAuthAlternatesService->updateUserAuthAlternatesRepository($matriculation, $user);
        }

        if ($user->activationStatusCode === UserStatus::INACTIVE) {
            $data['activationStatusCode'] = UserStatus::NON_VERIFIED;
            $hashedPassword               = $this->generatePassword(7, false, true, true, true);
            $data['password']             = bcrypt($hashedPassword);
            $this->generateAndRegisterVerificationCode($user);
        }

        $userUpdated = UserWriteRepository::updateUser($user, $data);

        if (isset($data['password']) && ! empty($data['password'])) {
            Mail::send(new UserPasswordChangedMail($user, $hashedPassword));
        }

        return $userUpdated;
    }

    public function detachAllPointsOfSale(User $user): ?User
    {
        $this->pointOfSaleService->detachAllPointsOfSale($user);
        return $user;
    }

    public function userByVerificationCode($verificationCode)
    {
        return $this->repository->findUserbyVerificationCode($verificationCode);
    }

    public function updateUserPassword(string $password, string $verificationCode): bool
    {
        $userVerificationCode = $this->repository->findUserbyVerificationCode($verificationCode);
        $userEntity           = $this->repository->find($userVerificationCode);

        if ($userEntity && $userEntity->isVerified($userVerificationCode)) {
            $userEntity->update(['password' => bcrypt($password)]);
            $this->repository->deleteVerificationCode($verificationCode);

            return true;
        }
        return false;
    }

    public function resetUserPassword(int $id): void
    {
        $user           = $this->repository->find($id);
        $hashedPassword = $this->generatePassword(7, false, true, true, true);
        $cpfBcrypt      = bcrypt($hashedPassword);
        $user->update(
            [
                'activationStatusCode' => UserStatus::NON_VERIFIED,
                'password'             => $cpfBcrypt,
                'signinAttempts'       => 0
            ]
        );
        Mail::send(new UserPasswordChangedMail($user, $hashedPassword));
    }

    public function resetUserPasswordUsingCpfByUser(User $user): void
    {
        $cpfBcrypt = bcrypt(mb_substr($user->cpf, 0, 6));
        $user->update(
            [
                'activationStatusCode' => UserStatus::NON_VERIFIED,
                'password'             => $cpfBcrypt,
                'signinAttempts'       => 0
            ]
        );
        Mail::send(new UserPasswordChangedMail($user, trans('messages.user.first_six_digits_of_cpf')));
    }

    public function verifyAccount($password, $verificationCode): bool
    {
        $user = $this->repository->findUserbyVerificationCode($verificationCode);
        $user = $this->repository->find($user['_id']);

        if ($user) {
            $this->repository->update($user, [
                'password'             => bcrypt($password),
                'activationStatusCode' => UserStatus::VERIFIED
            ]);

            $this->repository->deleteVerificationCode($verificationCode);

            return true;
        }

        return false;
    }

    public function activateUser(string $verificationCode, string $password): bool
    {
        $userId = $this->repository->findUserByVerificationCode($verificationCode);
        $user   = $this->repository->find($userId);
        if ($user) {
            $this->repository->update(
                $user,
                ['password' => bcrypt($password), 'activationStatusCode' => UserStatus::ACTIVE]
            );

            $this->repository->deleteVerificationCode($verificationCode);

            return true;
        }

        return false;
    }

    public function changeUserStatus(?string $cpf, string $status): bool
    {
        $user = $this->repository->findOneBy('cpf', $cpf);
        if ($user) {
            $this->repository
                ->update($user, ['activationStatusCode' => $status]);

            return true;
        }

        return false;
    }

    public function logSuccessAttempt(string $cpf): void
    {
        $user             = $this->findBy($cpf);
        $user->timestamps = false;
        if ($user) {
            $this->repository->resetSigninAttempts($user);
        }
    }

    public function findBy($cpf): ?User
    {
        return $this->repository->findOneBy('cpf', $cpf);
    }

    public function logFailAttempt(string $cpf = ''): void
    {
        $user = $this->findBy($cpf);
        if ($user) {
            $this->repository->incrementSigninAttempt($user);
        }
    }

    public function findOneByCpf($cpf): ?User
    {
        $user = $this->repository->findOneBy('cpf', $cpf);
        if (! $user instanceof User) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function findOneByAlternate($alternateDocument): ?User
    {
        return User::whereHas('userAuthAlternate', static function (Builder $builder) use ($alternateDocument) {
            $builder->where('document', '=', $alternateDocument);
        })->first();
    }

    public function findOneByCpfWithTrashed(string $cpf): ?User
    {
        return $this->repository->findOneByWithTrash('cpf', $cpf);
    }

    public function findManyByPointOfSale(array $pointsOfSaleIds): ?Builder
    {
        return $this->repository->findMany('pointsOfSale', 'pointsOfSaleId', $pointsOfSaleIds);
    }

    public function getUserContext(string $subSystem, string $module): string
    {
        $user = $this->repository->getAuthenticatedUser();
        return ContextHelper::getContext($user->role->permissions, $subSystem, $module);
    }

    public function getAuthenticatedUser(): ?User
    {
        return $this->repository->getAuthenticatedUser();
    }

    public function getSalesmen(): Collection
    {
        return $this->repository
            ->findUsersByAPIPermission(SalePermission::getFullName(PermissionActions::CREATE))
            ->where('activationStatusCode', '=', UserStatus::ACTIVE)
            ->get();
    }

    public function findByPermission(Builder $users, string $permission)
    {
        return $this->repository->findUsersWithPermission($users, $permission);
    }

    public function prepareAndUpdateUser(array $data, string $cpf): ?User
    {
        $user = $this->userService->findOneByCpf($cpf);

        return $this->updateUser($user, $data);
    }

    public function showUser($cpf): User
    {
        $this->findOneByCpf($cpf);

        $user = $this->repository->filter(['cpf' => $cpf])->get()->first();

        if ($user) {
            return $user;
        }

        throw UserExceptions::userAuthHasNotAuthorizationUnderUser();
    }

    public function getUserImportableType($request, $importType)
    {
        $rules      = resolve(BusinessRules::class)->setAuthorizations($request->user());
        $importable = ImportableFactory::make($importType, ['businessRules' => $rules]);
        $engine     = new ImportEngine($importable);
        $file       = $request->file('file');
        $errors     = $engine->process($file);

        return $errors;
    }

    public function makeActiveToken(string $token): void
    {
        $value       = md5($token);
        $currentUser = $this->getAuthenticatedUser();
        $user        = $this->repository->find($currentUser->id);

        $this->repository->update(
            $user,
            [
                'activeToken' => $value
            ]
        );
    }

    public function createAccessLog(array $data, int $id): void
    {
        $data['userId'] = $id;
        $this->accessLogRepository->create($data);
    }

    public function getUserByCpf(?string $cpf): ?User
    {
        return $this->repository->whereBy('cpf', $cpf)->first();
    }
}
