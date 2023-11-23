<?php

namespace TradeAppOne\Http\Controllers\Components;

use Banner\Http\Request\BannerBulkEditFormRequest;
use Banner\Http\Request\BannerEditFormRequest;
use Banner\Http\Request\BannerFormRequest;
use Banner\Service\BannerService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BannerController
{
    protected $service;

    public function __construct(BannerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $key  = filled($role) ? $user->getNetwork()->slug : '';
        return $this->service->getAll($key);
    }

    public function admin()
    {
        $user = Auth::user();
        $role = $user->role;
        $key  = filled($role) ? $user->getNetwork()->slug : '';
        return $this->service->getAllAdmin($key);
    }

    public function store(BannerFormRequest $request)
    {
        $attributes        = $request->all();
        $pointOfSale       = Auth::user()->pointsOfSale()->first();
        $attributes['key'] = filled($pointOfSale) ? $pointOfSale->network->slug : '';
        $banner            = $this->service->store($attributes);
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
