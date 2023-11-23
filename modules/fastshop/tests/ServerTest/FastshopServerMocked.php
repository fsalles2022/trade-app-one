<?php


namespace FastShop\tests\ServerTest;

use ClaroBR\Connection\SivRoutes;
use ClaroBR\Tests\ClaroBRTestBook;
use FastShop\Connection\FastshopRoutes;
use FastShop\tests\FastshopTestBook;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class FastshopServerMocked
{
    private $path;
    private $params;
    private $query;
    private $queryArray;
    private $loggedUser;
    private $method;

    public function __invoke(RequestInterface $req, array $options)
    {
        $this->path   = $req->getUri()->getPath();
        $this->params = $req->getBody()->getContents();
        $this->query  = $req->getUri()->getQuery();
        parse_str($this->query, $this->queryArray);
        $this->method     = $req->getMethod();
        $this->loggedUser = Auth::user();

        $routes = $this->fastServerRoutes();

        $status = 200;

        $body = array_key_exists($this->path, $routes)
            ? $routes[$this->path]
            : stream_for('{}');

        return new FulfilledPromise(
            new Response($status, ['Content­Type' => 'application/json'], $body)
        );
    }

    private function fastServerRoutes(): array
    {
         return [
             FastshopRoutes::LIST_PRODUCTS => $this->productsList(),
             FastshopRoutes::DEVICE_PRICE  => $this->productPrice(),
             FastshopRoutes::AUTH          => $this->authenticate()
         ];
    }

    private function fetchFile(string $path): Stream
    {
        return stream_for(file_get_contents($path));
    }

    private function matchIn(string $query, string $value): bool
    {
        return (bool) preg_match('/' . $value . '/', $query);
    }

    private function authenticate(): Stream
    {
        return stream_for("{\"access_token\": \"gxvGN2ioz3vMaTrQiALofXlVbnnH\"}");
    }

    private function productsList(): Stream
    {
        $page = data_get($this->queryArray, 'page', 0);
        return $page == 0 ? $this->fetchFile(FastshopResponseBook::SUCCESS_LIST_PRODUCTS) : stream_for('[]');
    }

    private function productPrice(): Stream
    {
        $idLoja = data_get($this->queryArray, 'id_loja', 0);
        $sku    = data_get($this->queryArray, 'sku', 0);

        return ($idLoja === FastshopTestBook::SUCCESS_POS && $sku === FastshopTestBook::SUCCESS_DEVICE) ?
            $this->fetchFile(FastshopResponseBook::SUCCESS_PRODUCT_PRICE) :
            $this->fetchFile(FastshopResponseBook::INVALID_PRODUCT_PRICE);
    }
}
