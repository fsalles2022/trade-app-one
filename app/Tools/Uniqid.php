<?php

namespace TradeAppOne\Tools;

class Uniqid
{
    public function generate(): string
    {
        return uniqid();
    }
}
