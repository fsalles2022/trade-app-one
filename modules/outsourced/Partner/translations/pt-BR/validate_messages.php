<?php
use Outsourced\Partner\Http\Requests\AuthPartnerFormRequest;

return [
    AuthPartnerFormRequest::ACCESS_KEY_REQUIRED => 'AccessKey é uma informação obrigatória.',
    AuthPartnerFormRequest::TOKEN_REQUIRED => 'Token é uma informação obrigatória.',
    AuthPartnerFormRequest::ROUTE_REQUIRED => 'RouteKey caso presente deve conter o nome da rota desejada.'
];
