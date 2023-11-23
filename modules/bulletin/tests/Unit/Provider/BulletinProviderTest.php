<?php

namespace Bulletin\tests\Unit\Provider;

use Bulletin\Providers\BulletinProvider;
use TradeAppOne\Tests\TestCase;

class BulletinProviderTest extends TestCase
{
    public function test_should_return_an_instance(): void
    {
        $class     = new BulletinProvider(app());
        $className = get_class($class);

        $this->assertEquals(BulletinProvider::class, $className);
    }
}
