<?php

namespace TradeAppOne\Domain\Importables;

interface ImportableInterface
{
    public function getColumns();

    public function processLine($line);

    public function getType();
}
