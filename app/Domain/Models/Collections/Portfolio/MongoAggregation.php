<?php

namespace TradeAppOne\Domain\Models\Collections\Portfolio;

interface MongoAggregation
{
    public function toMongoAggregation(): array;
}
