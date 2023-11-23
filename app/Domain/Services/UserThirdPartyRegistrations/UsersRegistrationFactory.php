<?php

namespace TradeAppOne\Domain\Services\UserThirdPartyRegistrations;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Exceptions\SystemExceptions\UserRegistrationServiceNotFound;
use VivoBR\Services\UserRegistrationVivoService;

final class UsersRegistrationFactory
{
    public static $registration = [
        Operations::VIVO => UserRegistrationVivoService::class
    ];

    public static function make(string $choice): UserRegistrationService
    {
        try {
            return resolve(self::$registration[$choice]);
        } catch (\ErrorException $exception) {
            throw new UserRegistrationServiceNotFound();
        }
    }
}
