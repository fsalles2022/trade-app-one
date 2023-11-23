<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use Illuminate\Database\Seeder;

class ThirdPartyAccessRiachueloSeeder extends Seeder
{
    use ThirdPartyAccess;

    const NETWORK     = NetworkEnum::RIACHUELO;

    const ROLE_NAME   = "Integrador Riachuelo";
    const ROLE_SLUG   = "integrador-riachuelo";
    const ROLE_PARENT = "gerente-rede-riachuelo";

    const FIRST_NAME  = "Integrador";
    const LAST_NAME   = "Riachuelo";
    const EMAIL       = "riachuelo@riachuelo.com.br";
    const USER_CPF    = "00000001406";
    const STATUS_CODE = UserStatus::ACTIVE;
}