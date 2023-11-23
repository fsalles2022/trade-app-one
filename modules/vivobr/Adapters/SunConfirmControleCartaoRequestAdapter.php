<?php

namespace VivoBR\Adapters;

use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;

class SunConfirmControleCartaoRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null): array
    {
        $operatorIdentifiers = $service->operatorIdentifiers;
        throw_if(is_null($operatorIdentifiers), new ServiceNotIntegrated());
        $status = $extra['status'] == 'SUCCESS' ? 'APROVADO' : 'REPROVADO';
        return [
            "servico" => "{$operatorIdentifiers['idVenda']}-{$operatorIdentifiers['idServico']}",
            "status"  => $status
        ];
    }
}
