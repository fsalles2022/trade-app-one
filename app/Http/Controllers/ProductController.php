<?php

namespace TradeAppOne\Http\Controllers;

use Discount\Services\ProductService;
use Illuminate\Http\Request;
use TradeAppOne\Domain\Adapters\ProductWithOperationsAdapter;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getAvailableServices()
    {
        return $this->productService->availableServices(auth()->user());
    }

    public function getFilterProducts(Request $request)
    {
        $user = $request->user();
        return $this->productService->filter($user, $request->all());
    }

    public function getAvailableServicesFormated()
    {
        $services = $this->productService->availableServices(auth()->user());
        return ProductWithOperationsAdapter::adapter($services);
    }
}
