<?php

use Outsourced\ViaVarejo\Exceptions\ViaVarejoExceptions;

return [
    ViaVarejoExceptions::COUPON_NOT_FOUND => 'Não foi possível encontrar um cupom válido para a triangulação.',
    ViaVarejoExceptions::UNAVAILABLE => 'Não foi possível conectar-se aos serviços da ViaVarejo.',
    ViaVarejoExceptions::SERVICE_NOT_FOUND => 'Serviço não encontrado, verifique o tipo de operação, ativação, portabilidade, migração.',
    ViaVarejoExceptions::NOT_ALLOWED => 'Serviço não disponível para sua rede.'
];
