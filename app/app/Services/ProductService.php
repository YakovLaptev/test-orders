<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public const string PRODUCTS_CACHE_KEY = 'products_';
    public const string PRODUCTS_PRICES_CACHE_KEY = 'products_prices_';

    /**
     * @param object{name:null|string, sku:null|string, category:null|string} $filter
     */
    public function getFilteredPaginatedProducts(
        object $filter,
        int    $page = 1,
        int    $perPage = 20
    ): LengthAwarePaginator
    {
        $cacheKey = self::PRODUCTS_CACHE_KEY . md5(json_encode($filter));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

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

        $products = $productsQuery->paginate(perPage: $perPage, page: $page);

        Cache::tags('products')->put($cacheKey, $products, now()->addMinutes(60));

        return $products;
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

        return $productsStock->keyBy('id');
    }

    /**
     * @param array $productsIds
     * @return Collection<int, Product>
     *
     * @throws Exception
     */
    public static function getProductsPrice(array $productsIds): Collection
    {
        $cacheKey = self::PRODUCTS_PRICES_CACHE_KEY . md5(serialize($productsIds));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $productsPrices = Product::whereIn('id', $productsIds)
            ->get(['id', 'price'])->keyBy('id');

        Cache::tags('products-price')->put($cacheKey, $productsPrices, now()->addMinutes(60));

        return $productsPrices;
    }
}
