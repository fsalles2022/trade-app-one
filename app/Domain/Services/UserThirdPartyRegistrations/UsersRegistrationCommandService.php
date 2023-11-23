<?php

namespace TradeAppOne\Domain\Services\UserThirdPartyRegistrations;

use Illuminate\Support\Collection;

class UsersRegistrationCommandService
{
    protected $baseService;

    public function __construct(RegistrationManagementService $baseService)
    {
        $this->baseService = $baseService;
    }

    public function process(array $options = [])
    {
        $operator = data_get($options, 'operator');
        $method   = data_get($options, 'method');
        $user     = data_get($options, 'user');
        $resume   = collect();

        if ($operator) {
            $connection = UsersRegistrationFactory::make($operator);
            if ($user) {
                $resume = $this->baseService->syncOneInLocal($user, $connection);
            } else {
                $resume = $resume->merge($this->runMethod($method, $connection));
            }
        } else {
            if ($user) {
                foreach (UsersRegistrationFactory::$registration as $operator => $class) {
                    $connection = UsersRegistrationFactory::make($operator);
                    $resume     = $resume->merge($this->baseService->syncOneInLocal($user, $connection));
                }
            } else {
                foreach (UsersRegistrationFactory::$registration as $operator => $class) {
                    $connection = UsersRegistrationFactory::make($operator);
                    $resume     = $resume->merge($this->runMethod($method, $connection));
                }
            }
        }
        return $resume;
    }

    private function runMethod(?string $method, UserRegistrationService $service): Collection
    {
        if ($method == 'all') {
            return $this->baseService->syncAllSalesmenInTradeAppOne($service);
        } else {
            return $this->baseService->syncPendingRegistrations($service);
        }
    }
}
