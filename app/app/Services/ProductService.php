<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    /**
     * @param object{name:null|string, sku:null|string, category:null|string} $filter
     */
    public function getFilteredPaginatedProducts(
        object $filter,
        int    $page = 1,
        int    $perPage = 20
    ): LengthAwarePaginator
    {
        $productsQuery = Product::query();

        if ($filter->name) {
            $productsQuery->where('name', 'like', '%' . $filter->name . '%');
        }

        if ($filter->sku) {
            $productsQuery->where('sku', 'like', '%' . $filter->sku . '%');
        }

        if ($filter->category) {
            $productsQuery->where('category', 'like', '%' . $filter->category . '%');
        }

        return $productsQuery->paginate(perPage: $perPage, page: $page);
    }

    /**
     * @param Collection $productsStockNeed
     * @param bool $isLockStock - блокировать ли сток на изменение для других транзакций
     * @return Collection<int, Product>
     *
     * @throws Exception
     */
    public static function checkProductsStock(Collection $productsStockNeed, bool $isLockStock = false): Collection
    {
        $productsQuery = Product::whereIn('id', $productsStockNeed->keys());

        if ($isLockStock) {
            $productsQuery->lockForUpdate();
        }

        $productsStock = $productsQuery->get(['id', 'name', 'stock_quantity']);

        $productsStock->each(function (Product $product) use ($productsStockNeed) {
            if ($product->stock_quantity < $productsStockNeed->get($product->id)) {
                throw new Exception('Товар ' . $product->name . ' закончился');
            }
        });

        return $productsStock->pluck(['id', 'stock_quantity'])->keyBy('id');
    }
}
