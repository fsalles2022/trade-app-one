<?php

namespace TradeAppOne\Domain\Components\RestClient;

use Illuminate\Http\UploadedFile;

interface Rest
{
    public function get(string $url): Rest;

    public function post(string $url): Rest;

    public function put(string $url): Rest;

    public function delete(string $url): Rest;

    public function addHeaders(array $data): Rest;

    public function withData(array $data): Rest;

    public function withQuery(array $query): Rest;

    public function addFile(UploadedFile $file): Rest;

    public function execute();
}
