<?php

namespace TradeAppOne\Domain\Factories;

use Jenssegers\Mongodb\Connection;
use MongoDB\Collection;

class MongoDbConnector
{
    public function getCollection(string $collection): Collection
    {
        $config       = $this->getConfig();
        $databaseName = data_get($config, 'database');

        $connection = new Connection($config);
        $client     = $connection->getMongoClient();

        return ($client)
            ->selectDatabase($databaseName)
            ->selectCollection($collection);
    }

    private function getConfig()
    {
        return config('database.connections.mongodb');
    }
}
