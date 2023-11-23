<?php

namespace FastShop\tests\Feature;

use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use FastShop\Connection\FastshopHeaders;
use FastShop\Connection\FastshopHttpClient;
use FastShop\Connection\FastshopRoutes;
use FastShop\Exceptions\FastshopExceptions;
use FastShop\Models\Product;
use FastShop\tests\FastshopTestBook;
use FastShop\tests\Helpers\Builders\ProductBuilder;
use FastShop\tests\ServerTest\FastshopServerMocked;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response as HttpResponse;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class FastshopSimulationFeatureTest extends TestCase
{
    use AuthHelper;

    private const URI = '/fastshop/products/:device/pos/:pos';

    protected function setUp()
    {
        parent::setUp();
        $this->bindFastshopResponse();
    }


    /** @test */
    public function should_return_valid_simulation_when_have_device_and_pos_valid(): void
    {
        $this->makeProduct();
        $uri      = $this->buildSimulationURI(FastshopTestBook::SUCCESS_DEVICE, FastshopTestBook::SUCCESS_POS);
        $response = $this->authAs()->get($uri);

        $data = $response->json();
        $this->assertNotEmpty($data['filtros']);
        $this->assertNotEmpty($data['resultado']);
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
    }

    /** @test */
    public function should_return_invalid_simulation_when_have_device_and_pos_invalid(): void
    {
        $this->makeProduct();
        $uri      = $this->buildSimulationURI(FastshopTestBook::INVALID_DEVICE, FastshopTestBook::INVALID_POS);
        $response = $this->authAs()->get($uri);
        $data     = $response->json();
        $this->assertEquals(FastshopExceptions::GENERAL_API_ERROR, $data['shortMessage']);
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }

    /** @test */
    public function should_return_invalid_simulation_when_no_have_device(): void
    {
        $uri      = $this->buildSimulationURI(FastshopTestBook::SUCCESS_DEVICE, FastshopTestBook::SUCCESS_POS);
        $response = $this->authAs()->get($uri);
        $data     = $response->json();
        $this->assertEquals(FastshopExceptions::SIMULATE_EMPTY_DEVICE_PRICE, $data['shortMessage']);
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }

    private function bindFastshopResponse(): void
    {
        app()->bind(
            FastshopHttpClient::class,
            static function () {
                $mock    = new FastshopServerMocked();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new FastshopHttpClient($client);
            }
        );
    }

    private function makeProduct(): Product
    {
        return (new ProductBuilder())->withCode(11)->build();
    }

    private function buildSimulationURI(string $device, string $pos): string
    {
        $uri = self::URI;
        $uri = str_replace(array(':device', ':pos'), array($device, $pos), $uri);
        return $uri;
    }
}
