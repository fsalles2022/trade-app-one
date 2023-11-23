<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\CEPClient\CEPConnection;

class CepController extends Controller
{
    protected $connection;

    public function __construct(CEPConnection $connection)
    {
        $this->connection = $connection;
    }

    public function get(Request $request)
    {
        $cep = $request->get('cep');
        if ($cep) {
            list($response, $status) = $this->connection->get($cep);
            return \response()->json($response, $status);
        }
        return response([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
