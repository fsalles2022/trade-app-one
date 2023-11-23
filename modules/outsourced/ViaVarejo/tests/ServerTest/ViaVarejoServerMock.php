<?php


namespace Outsourced\ViaVarejo\tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Auth;
use Outsourced\ViaVarejo\tests\ViaVarejoTestBook;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class ViaVarejoServerMock
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

        $routes = $this->viaVarejoServerRoutes();

        $status = array_key_exists($this->path, $routes) ? $routes[$this->path]['statusCode'] : 200;

        $body = array_key_exists($this->path, $routes)
            ? $routes[$this->path]['method']
            : stream_for('{}');

        return new FulfilledPromise(
            new Response($status, ['Content­Type' => 'application/json'], $body)
        );
    }

    private function viaVarejoServerRoutes(): array
    {
        return [
            ViaVarejoTestBook::CHECK_CLIENT_URL . '/' . ViaVarejoTestBook::CPF_SHOULD_RETURN_200 => [
                'method' => $this->validateClientSuccess(),
                'statusCode' => 200
            ],
            ViaVarejoTestBook::CHECK_CLIENT_URL . '/' . ViaVarejoTestBook::CPF_SHOULD_RETURN_404 => [
                'method' => $this->validateClientFailed(),
                'statusCode' => 200
            ],
            ViaVarejoTestBook::CHECK_CLIENT_URL . '/' . ViaVarejoTestBook::CPF_SHOULD_RETURN_422 => [
                'method' => $this->validateClientUnavailable(),
                'statusCode' => 401
            ]
        ];
    }

    private function validateClientSuccess(): Stream
    {
        return $this->fetchFile(ViaVarejoResponseBook::SUCCESS_CLIENT_RETRIEVE);
    }

    private function validateClientFailed(): Stream
    {
        return $this->fetchFile(ViaVarejoResponseBook::FAILED_CLIENT_RETRIEVE);
    }

    private function validateClientUnavailable(): Stream
    {
        return $this->fetchFile(ViaVarejoResponseBook::INVALID_TOKEN_RETRIEVE);
    }

    private function fetchFile(string $path): Stream
    {
        return stream_for(file_get_contents($path));
    }
}
