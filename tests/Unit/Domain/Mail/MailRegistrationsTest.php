<?php

namespace TradeAppOne\Tests\Unit\Domain\Mail;

use Illuminate\Support\Facades\Queue;
use TradeAppOne\Mail\MailRegistrations;
use TradeAppOne\Tests\TestCase;

class MailRegistrationsTest extends TestCase
{
    /** @test */
    public function should_queued_mail_where_send_mail_registrations()
    {
        Queue::fake();
        Queue::assertNothingPushed();

        dispatch((new MailRegistrations(
            '',
            [],
            [],
            []
        )));

        Queue::assertPushed(MailRegistrations::class, 1);
    }
}
