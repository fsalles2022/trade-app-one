<?php

namespace TradeAppOne\Domain\Logging;

interface IntegrationLogger
{
    public function transaction($transaction): self;

    public function request($request): self;

    public function response($response): self;

    public function tags(array $tags): self;

    public function context(array $context): self;

    public function tag(string $key, string $value): self;

    public function extra(array $extra): self;

    public function message(string $message): self;

    public function getContext(): array;

    public function getExtra(): array;

    public function getMessage(): string;

    public function fire();
}
