<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

abstract class ElasticsearchStructureAbstract
{
    protected $structure;

    public function __construct()
    {
        $this->structure = [];
    }

    public function asArray(): array
    {
        return $this->structure;
    }

    public function asJSON(): string
    {
        return json_encode($this->structure);
    }
}
