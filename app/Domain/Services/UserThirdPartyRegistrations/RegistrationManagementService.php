<?php

namespace TradeAppOne\Domain\Services\UserThirdPartyRegistrations;

use Illuminate\Support\Collection;
use TimBR\Exceptions\EligibilityNotFound;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Domain\Services\Interfaces\UserThirdPartyRepository;

class RegistrationManagementService extends BaseService
{
    public function syncPendingRegistrations(UserRegistrationService $registrationService): Collection
    {
        $usersToRegister = $this->userThirdPartyRegistrationService
            ->getSalesmenUnregisteredFrom($registrationService->getOperator());

        return $this->syncUsersFromTable($registrationService, $usersToRegister);
    }

    protected function syncUsersFromTable(UserRegistrationService $registrationService, $usersToRegister): Collection
    {
        $collectionOfResults = new Collection();
        foreach ($usersToRegister as $userToRegister) {
            $user               = $userToRegister->user;
            $currentPointOfSale = $userToRegister->pointOfSale;
            try {
                list($action, $status) = $registrationService->runOneInAPI($user, $currentPointOfSale);
                $registerUpdated       = $this->userThirdPartyRegistrationService
                    ->flag($userToRegister, $status);
                $result                = [
                    'status'   => $status,
                    'action'   => $action,
                    'register' => $registerUpdated
                ];
                $collectionOfResults->push($result);
            } catch (\Exception $exception) {
                $registerUpdated = $this->userThirdPartyRegistrationService
                    ->flag(
                        $userToRegister,
                        false
                    );
                $errorResult     = [
                    'status'   => false,
                    'message'  => $exception->getMessage(),
                    'register' => $registerUpdated
                ];
                $collectionOfResults->push($errorResult);
            }
        }
        return $collectionOfResults;
    }

    public function syncOneInLocal(string $cpf, UserRegistrationService $registrationService): Collection
    {
        $collection          = collect();
        $user                = $this->userService->findOneByCpf($cpf);
        $currentPointOfSale  = $user->pointsOfSale->first();
        $pendingRegistration = $this->userThirdPartyRegistrationService
            ->getPendingRegistrationOf(
                $user,
                $registrationService->getOperator()
            )->first();

        try {
            list($action, $status) = $registrationService->runOneInAPI($user, $currentPointOfSale);
        } catch (\Exception $exception) {
            $status = false;
            $log    = $exception->getMessage();
            $this->persist($registrationService, $pendingRegistration, $currentPointOfSale, $user, $status, $log);
        }
        $collection->push(compact('action', 'status'));
        $this->persist($registrationService, $pendingRegistration, $currentPointOfSale, $user, $status);
        return $collection;
    }

    private function persist(
        UserRegistrationService $registrationService,
        $pendingRegistration,
        $currentPointOfSale,
        $user,
        $status,
        $log = ''
    ): void {
        if (is_null($pendingRegistration) || $pendingRegistration->pointOfSale->id != $currentPointOfSale->id) {
            $currentPointOfSale  = $user->pointsOfSale()->first();
            $pendingRegistration = $this->userThirdPartyRegistrationService->create(
                $user,
                $currentPointOfSale,
                $registrationService->getOperator(),
                $log
            );
        }
        $this->userThirdPartyRegistrationService->flag($pendingRegistration, $status, $log);
    }

    public function syncAllSalesmenInTradeAppOne(UserRegistrationService $registrationService = null): Collection
    {
        $resume   = new Collection();
        $salesmen = $this->userThirdPartyRegistrationService->getSalesmen();
        foreach ($salesmen as $user) {
            try {
                $pointOfSale           = $user->pointsOfSale()->first();
                $pendingRegistration   = $this->userThirdPartyRegistrationService->create(
                    $user,
                    $pointOfSale,
                    $registrationService->getOperator()
                );
                list($action, $status) = $registrationService->runOneInAPI($user, $pointOfSale);
                $this->userThirdPartyRegistrationService->flag($pendingRegistration, $status);
                $result = compact('action', 'status');
                $resume->push($result);
            } catch (\Exception $exception) {
                $error = ['status' => false, 'message' => $exception->getMessage()];
                $resume->push($error);
            }
        }
        $this->logResume($resume);
        return $resume;
    }

    private function logResume(Collection $resume)
    {
        $log = $this->getResume($resume);
        logger()->info('sync-users', $log);
    }

    public function getResume(Collection $resume)
    {
        $success = $resume->where('status', true)->count();
        $error   = $resume->where('status', false)->count();
        $created = $resume->where('action', UserThirdPartyRepository::CREATED)->count();
        $synced  = $resume->where('action', UserThirdPartyRepository::UPDATED)->count();
        return compact('success', 'created', 'synced', 'error');
    }
}
