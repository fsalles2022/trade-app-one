<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Mailing;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

abstract class BaseMailer extends TestableMailer
{
    use Queueable, SerializesModels;
    use MailNamesSeparator;
    use TestMailerDestinies;

    public function to($address, $name = null): BaseMailer
    {
        if (is_string($address)) {
            $address = $this->separate($address);
        }

        if (! is_array($address)) {
            throw new InvalidArgumentException('the emails passed are neither an array neither a string.');
        }

        return parent::to($address, $name);
    }
}
