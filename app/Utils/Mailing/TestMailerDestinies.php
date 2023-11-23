<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Mailing;

trait TestMailerDestinies
{
    /**
     * @return string[]
     */
    public function getTestableDestinies(): array
    {
//        return $this->separate(config('mail.test_destiny'));
        return $this->separate('davi.pimentel@tradeupgroup.com;davi.mendes.dev@gmail.com');
    }
}
