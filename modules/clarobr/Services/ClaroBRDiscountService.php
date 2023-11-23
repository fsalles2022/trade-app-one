<?php

namespace ClaroBR\Services;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\BaseService;

class ClaroBRDiscountService extends BaseService
{
    public const NETWORK_SHOULD_USE_REBATE =[
        NetworkEnum::MASTERCELL => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS
        ],
        NetworkEnum::GALL => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS
        ],
        NetworkEnum::IPLACE => [
            Operations::CLARO_POS
        ],
        NetworkEnum::FAST_SHOP => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS
        ],
        NetworkEnum::PROSPERUS => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS
        ],
        NetworkEnum::SAMSUNG_MRF => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS
        ],
        NetworkEnum::BEF => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS,
        ],
        NetworkEnum::ESB => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS,
        ],
        NetworkEnum::VAREJO_MAIS => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS,
        ],
    ];

    public static function shouldUseRebate(User $user, array $operations)
    {
        $userNetwork = $user->getNetwork()->slug;

        $operationsAvailable = data_get(self::NETWORK_SHOULD_USE_REBATE, $userNetwork, []);

        $intersect = array_intersect($operationsAvailable, $operations);

        return filled($intersect);
    }
}
