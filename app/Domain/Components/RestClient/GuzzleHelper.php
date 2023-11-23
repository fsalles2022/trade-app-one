<?php

namespace TradeAppOne\Domain\Components\RestClient;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;

class GuzzleHelper implements Rest
{
    private $client;
    private $url;
    private $type;
    public $options;

    public function __construct(Client $client)
    {
        $this->client  = $client;
        $this->options = [];
    }

    public function get(string $url): Rest
    {
        $this->type = 'get';
        $this->url  = $url;

        return $this;
    }

    public function post(string $url): Rest
    {
        $this->type = 'post';
        $this->url  = $url;

        return $this;
    }

    public function put(string $url): Rest
    {
        $this->type = 'put';
        $this->url  = $url;

        return $this;
    }

    public function delete(string $url): Rest
    {
        $this->type = 'delete';
        $this->url  = $url;

        return $this;
    }

    public function addHeaders(array $headers): Rest
    {
        $this->options['headers'] = $headers;

        return $this;
    }

    public function withData(array $data): Rest
    {
        $this->options['json'] = $data;

        return $this;
    }

    public function withQuery(array $query): Rest
    {
        $this->options['query'] = $query;

        return $this;
    }

    public function addFile(UploadedFile $file): Rest
    {
        $this->options['multipart'][] = [
            'Content-type' => 'multipart/form-data',
            'name'         => 'file',
            'contents'     => file_get_contents($file),
            'filename'     => $file->getClientOriginalName(),
        ];

        return $this;
    }

    public function execute()
    {
        $type = $this->type;

        return $this->client->$type($this->url, $this->options);
    }
}
