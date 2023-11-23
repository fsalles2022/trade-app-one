<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Mailing;

trait MailNamesSeparator
{
    /**
     * @param string|null $emails
     * @return string[]
     */
    public function separate(string $emails = null): array
    {
        return explode(self::getSeparator(), $emails);
    }

    public static function getSeparator(): string
    {
        $separatorChar = config('mail.separator');

        if (! $separatorChar) {
            throw new \InvalidArgumentException("'mail.separator' not properly configured on 'config/mail.php'");
        }

        return $separatorChar;
    }
}
