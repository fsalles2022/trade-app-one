<?php

namespace Outsourced\ViaVarejo\tests;

use Outsourced\ViaVarejo\Connections\ViaVarejoRoutes;

final class ViaVarejoTestBook
{
    public const SUCCESS_SKU             = 'AMB628982944ZA';
    public const INVALID_SKU             = 'AMB999982988ZB';
    public const CPF_SHOULD_RETURN_404   = '63707615081';
    public const CPF_SHOULD_RETURN_422   = '63707616764';
    public const CPF_SHOULD_RETURN_200   = '04155519194';
    public const CHECK_CLIENT_URL        = ViaVarejoRoutes::VALIDATE;
    public const RESPONSE_USER_NOT_FOUND = [
        "shortMessage" => "ViaVarejoCustomerNotFound",
        "message"=> "Cadastrar o cliente no VIA+ antes de  realizar a venda do plano",
        "description" => null,
        "help" => "",
        "transportedMessage"=> "",
        "transportedData"=> ""
    ];

    public const RESPONSE_NOT_ALOWED = [
        "shortMessage" => "ViaVarejoServiceNotAllowed",
        "message" => "Serviço não disponível para sua rede.",
        "description" => null,
        "help" => "",
        "transportedMessage" => "",
        "transportedData" => ""
    ];

    public const RESPONSE_UNPROCESSABLE_ENTITY = [
        "shortMessage" => "viaVarejoUnavailable",
        "message" => "Não foi possível conectar-se aos serviços da ViaVarejo.",
        "description" => null,
        "help" => "",
        "transportedMessage" => "",
        "transportedData" => ""
    ];
}
