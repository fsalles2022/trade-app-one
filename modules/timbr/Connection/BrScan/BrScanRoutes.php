<?php

declare(strict_types=1);

namespace TimBR\Connection\BrScan;

class BrScanRoutes
{
    public const GENERATE_AUTHENTICATE_LINK    = '/analise-rest/gerar-link-captura';
    public const AUTHENTICATE_STATUS           = '/analise-rest/enviar-analise-biometria';
    public const SEND_SALE_FOR_TERM_SIGNATURE  = '/varejo-premium-rest/cadastrar-venda-varejo-premium';
    public const TERM_SIGNATURE_STATUS         = '/varejo-premium-rest/enviar-aceite-tradeup';
    public const SEND_WELCOME_KIT_FOR_CUSTOMER = '/varejo-premium-rest/enviar-kit-boas-vindas-varejo-premium';
}
