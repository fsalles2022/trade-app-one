<?php

namespace Banner\Http\BannerController;

use Banner\Http\Request\BannerBulkEditFormRequest;
use Banner\Http\Request\BannerEditFormRequest;
use Banner\Http\Request\BannerFormRequest;
use Banner\Service\BannerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Http\Controllers\Controller;

class BannerController extends Controller
{
    protected $service;

    public function __construct(BannerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->service->getAll($request->get('key'));
    }

    public function admin(Request $request)
    {
        return $this->service->getAllAdmin($request->get('key'));
    }

    public function store(BannerFormRequest $request)
    {
        $attributes = $request->all();
        $banner     = $this->service->store($attributes);
        return response()->json(['banner' => $banner]);
    }

    public function edit($id, BannerEditFormRequest $request)
    {
        $banner = $this->service->edit($id, $request->all());
        return response()->json(['banner' => $banner]);
    }

    public function bulkEdit(BannerBulkEditFormRequest $request)
    {
        $banners = $this->service->bulkEdit($request->except('requestId'));
        return response()->json(['banners' => $banners]);
    }

    public function show($id)
    {
        return $this->service->show($id);
    }

    public function destroy($id)
    {
        if ($this->service->destroy($id)) {
            return response()->json(['message' => '']);
        }
        return response()->json(['message' => trans('banner::messages.destroy')], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
