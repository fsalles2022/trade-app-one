<?php

namespace Outsourced\Partner\tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Auth;
use Outsourced\Partner\tests\AuthPartnerTestBook;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class PartnerServerMock
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

        $routes = $this->partnerServerRoutes();

        $status = 200;

        $body = array_key_exists($this->path, $routes)
            ? $routes[$this->path]
            : stream_for('{}');

        return new FulfilledPromise(
            new Response($status, ['Content­Type' => 'application/json'], $body)
        );
    }

    private function partnerServerRoutes(): array
    {
        return [
            AuthPartnerTestBook::CREDENTIAL_VERIFY_URL_URI => $this->userDocument()
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

    private function userDocument(): Stream
    {
        return $this->fetchFile(PartnerResponseBook::SUCCESS_USER_DOCUMENT);
    }
}
