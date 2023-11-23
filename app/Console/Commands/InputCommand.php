<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;

class InputCommand
{
    public function confirmSaleQuantity(Command $command, $quantity)
    {
        return $command->confirm("Foram encontradas $quantity vendas. Continuar?");
    }
}
