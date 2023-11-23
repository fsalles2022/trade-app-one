<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Mailing;

use Illuminate\Mail\Mailable;
use RuntimeException;
use TradeAppOne\Utils\Testing\ITestable;
use TradeAppOne\Utils\Testing\Testable;

abstract class TestableMailer extends Mailable implements ITestable
{
    use Testable;

    /**
     * @return string[]
     */
    abstract public function getTestableDestinies(): array;

    public function build(): Mailable
    {
        if ($this->isTest()) {
            $this->flushDestinies()->to($this->getTestableDestinies());
        }

        if ($this->isMocked()) {
            throw new RuntimeException("Email class doesn't work with mock from object, if you want to mock emails, please, use Mail::fake before sending the email.");
        }

        return $this;
    }

    protected function flushDestinies(): Mailable
    {
        $this->to = [];
        return $this;
    }
}
