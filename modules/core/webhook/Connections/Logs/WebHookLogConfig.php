<?php

namespace Core\WebHook\Connections\Logs;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticSearchConfig;

class WebHookLogConfig implements ElasticSearchConfig
{
    public function getHost(): string
    {
        return config('heimdall.host');
    }

    public function getPort(): string
    {
        return config('heimdall.port');
    }

    public function getType(): string
    {
        return '_doc';
    }

    public function getIndex(): string
    {
        return 'heimdall_webhooks';
    }
}
