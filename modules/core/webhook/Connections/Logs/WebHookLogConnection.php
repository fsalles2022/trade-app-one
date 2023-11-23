<?php

namespace Core\WebHook\Connections\Logs;

use Exception;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticSearch;
use TradeAppOne\Domain\HttpClients\Responseable;

class WebHookLogConnection
{
    protected $elasticSearch;
    protected $destiny;
    protected $configs;
    protected $response;
    protected $request;

    public function __construct(ElasticSearch $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function destiny(string $destiny): WebHookLogConnection
    {
        $this->destiny = $destiny;
        return $this;
    }

    public function configs(array $configs): WebHookLogConnection
    {
        $headers            = data_get($configs, 'headers', []);
        $configs['headers'] = array_keys($headers);

        $this->configs = $configs;
        return $this;
    }

    public function request(array $request): WebHookLogConnection
    {
        $this->request['content'] = json_encode($request);
        return $this;
    }

    public function response(Responseable $response): WebHookLogConnection
    {
        $this->response['content'] = $response->get();
        $this->response['status']  = $response->getStatus();
        $this->response['success'] = $response->isSuccess();

        return $this;
    }

    public function exception(Exception $exception): WebHookLogConnection
    {
        $this->response['success'] = false;
        $this->response['status']  = $exception->getCode();
        $this->response['message'] = $exception->getMessage();
        $this->response['line']    = $exception->getLine();
        $this->response['file']    = $exception->getFile();
        $this->response['trace']   = $exception->getTrace();

        return $this;
    }

    public function adapter(): array
    {
        return array_filter([
            'destiny'  => $this->destiny,
            'configs'  => $this->configs,
            'response' => $this->response,
            'request'  => $this->request,
            'datetime' => now()->toIso8601String()
        ]);
    }

    public function save(): void
    {
        try {
            $this->elasticSearch->index($this->adapter());
        } catch (Exception $exception) {
            Log::info('WebHook ElasticSearch is down: ' . $exception->getMessage());
        }
    }
}
