<?php

namespace ClaroBR\Connection;

class VertexConnection implements VertexConnectionInterface
{
    private const VERTEX_SOURCE = 'tradeupvarejonegados';
    protected $vertexClient;

    public function __construct(VertexHttpClient $vertexClient)
    {
        $this->vertexClient = $vertexClient;
    }


    public function sendData(array $data)
    {
        $payload = [
            'source' => self::VERTEX_SOURCE,
            'data' => $data
        ];
        return $this->vertexClient->post(VertexRoutes::SEND, $payload);
    }
}
