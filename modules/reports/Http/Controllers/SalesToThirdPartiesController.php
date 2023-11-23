<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\SalesToThirdPartiesService;

class SalesToThirdPartiesController
{
    private $salesToThirdPartiesService;

    public function __construct(SalesToThirdPartiesService $service)
    {
        $this->salesToThirdPartiesService = $service;
    }

    public function index(Request $request)
    {
        try {
            $response = $this->salesToThirdPartiesService->getSales($request->all());
            return response()->json($response, Response::HTTP_OK);
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
