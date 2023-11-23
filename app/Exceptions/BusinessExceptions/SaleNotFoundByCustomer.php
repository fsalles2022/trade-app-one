<?php

declare(strict_types=1);

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Symfony\Component\HttpFoundation\Response;

class SaleNotFoundByCustomer extends BusinessRuleExceptions
{
    public function getHelp(): string
    {
        return 'Não há vendas encontradas vinculadas ao cliente';
    }

    public function getDescription(): string
    {
        return 'Não há vendas encontradas vinculadas ao cliente';
    }

    public function getShortMessage(): string
    {
        return 'SaleNotFoundByCustomer';
    }

    public function getHttpStatus(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
