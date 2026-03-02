<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Products\ProductRequest;
use App\Http\Resources\PaginatedProductsResource;
use App\Services\ProductService;

class ProductsController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function getList(ProductRequest $request): PaginatedProductsResource
    {
        $productsData = $this->service->getFilteredPaginatedProducts($request->makeFilter());

        return PaginatedProductsResource::make($productsData);
    }
}
