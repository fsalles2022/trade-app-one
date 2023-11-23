<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use Illuminate\Database\Seeder;

class ThirdPartyAccessLebesSeeder extends Seeder
{
    use ThirdPartyAccess;

    const NETWORK     = NetworkEnum::LEBES;

    const ROLE_NAME   = "Integrador Lebes";
    const ROLE_SLUG   = "integrador-lebes";
    const ROLE_PARENT = "gerente-de-rede";

    const FIRST_NAME  = "Integrador";
    const LAST_NAME   = "Lebes";
    const EMAIL       = "lebes@lebes.com.br";
    const USER_CPF    = "00000001910";
    const STATUS_CODE = UserStatus::ACTIVE;
}