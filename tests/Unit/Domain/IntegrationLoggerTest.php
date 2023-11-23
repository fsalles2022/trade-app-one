<?php

namespace TradeAppOne\Tests\Unit\Domain;

use TradeAppOne\Domain\Logging\IntegrationConcrete;
use TradeAppOne\Tests\TestCase;

class IntegrationLoggerTest extends TestCase
{
    /** @test */
    public function should_return_tags_inside_context_attribute()
    {
        $assertTags = ['tag1' => 'value'];
        $logger     = new IntegrationConcrete();

        $context = $logger->tags($assertTags)->getContext();

        self::assertEquals($assertTags, $context['tags']);
    }

    /** @test */
    public function should_return_message_inside_message_attribute()
    {
        $assertMessage = 'Message';
        $logger        = new IntegrationConcrete();

        $logger->message($assertMessage);
        $assertMessageReturn = $logger->getMessage();

        self::assertEquals($assertMessage, $assertMessageReturn);
    }

    /** @test */
    public function should_return_extra_inside_extra_attribute()
    {
        $assertExtra = ['extra' => 'value', 'extra1' => ['value', 'value1']];
        $logger      = new IntegrationConcrete();

        $logger->extra($assertExtra);
        $context = $logger->getContext();
        $extra   = $logger->getExtra();

        self::assertEquals($assertExtra, $extra);
    }

    /** @test */
    public function should_return_message_with_tags_inside_context()
    {
        $assertTags    = ['tag1' => 'value'];
        $assertMessage = 'Message';
        $logger        = new IntegrationConcrete();

        $logger->message($assertMessage)->tags($assertTags);
        $context             = $logger->getContext();
        $assertMessageReturn = $logger->getMessage();

        self::assertEquals($assertTags, $context['tags']);
        self::assertEquals($assertMessage, $assertMessageReturn);
    }

    /** @test */
    public function should_return_another_context_with_tags_inside_context()
    {
        $assertContext = ['context1' => 'value', 'context2' => ['value', 'value1']];
        $assertTags    = ['tag1' => 'value'];
        $logger        = new IntegrationConcrete();

        $logger->context($assertContext)->tags($assertTags);
        $context = $logger->getContext();

        self::assertEquals($assertTags, $context['tags']);
        unset($context['tags']);
        self::assertEquals($assertContext, $context);
    }

    /** @test */
    public function should_return_context_empty_inside_context_attribute()
    {
        $assertExtra = ['extra' => 'value', 'extra1' => ['value', 'value1']];
        $logger      = new IntegrationConcrete();

        $logger->extra($assertExtra);
        $context = $logger->getContext();

        self::assertEquals([], $context);
    }

    /** @test */
    public function should_return_sale_transaction_inside_context_attribute()
    {
        $assertServiceTransaction = '12345678';
        $logger                   = new IntegrationConcrete();

        $logger->transaction($assertServiceTransaction);
        $context = $logger->getContext();

        self::assertEquals($assertServiceTransaction, $context['tags']['serviceTransaction']);
    }

    /** @test */
    public function should_return_sale_transaction_filled_inside_context_attribute()
    {
        $assertServiceTransaction = '12345678';
        $logger                   = new IntegrationConcrete();

        $logger->transaction($assertServiceTransaction);
        $context = $logger->getContext();

        self::assertNotEmpty($assertServiceTransaction, $context['tags']['serviceTransaction']);
    }
}
