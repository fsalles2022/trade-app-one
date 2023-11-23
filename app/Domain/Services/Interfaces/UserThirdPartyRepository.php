<?php

namespace TradeAppOne\Domain\Services\Interfaces;

interface UserThirdPartyRepository
{
    const UPDATED = 'UPDATED';
    const CREATED = 'CREATED';

    public function findUser(array $keywords): array;

    public function createOrUpdate(array $attributes, $extra = null): array;
}
