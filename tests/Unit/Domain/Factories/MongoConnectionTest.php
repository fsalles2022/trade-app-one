<?php

namespace TradeAppOne\Tests\Unit\Domain\Factories;

use MongoDB\Collection;
use TradeAppOne\Domain\Factories\MongoDbConnector;
use TradeAppOne\Domain\Factories\MongoDbOptions;
use TradeAppOne\Domain\Repositories\Collections\SalePaginatedRepository;
use TradeAppOne\Tests\TestCase;

class MongoConnectionTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $class     = new MongoDbConnector();
        $className = get_class($class);
        $this->assertEquals(MongoDbConnector::class, $className);
    }

    /** @test */
    public function should_get_collection_return_an_instance_of_collection()
    {
        $class     = new MongoDbConnector();
        $className = $class->getCollection(SalePaginatedRepository::COLLECTION_NAME);
        $this->assertEquals(Collection::class, get_class($className));
    }
}
