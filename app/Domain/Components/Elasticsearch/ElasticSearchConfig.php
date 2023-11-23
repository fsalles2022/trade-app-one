<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

interface ElasticSearchConfig
{
    public function getHost(): string;
    public function getPort(): string;
    public function getType(): string;
    public function getIndex(): string;
}
