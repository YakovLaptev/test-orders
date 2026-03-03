<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @mixin LengthAwarePaginator */
class PaginatedProductsResource extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'items' => ProductResource::collection($this->items()),
            'pagination' => [
                'per_page' => $this->perPage(),
                'page' => $this->currentPage(),
                'total' => $this->total(),
                'total_page' => $this->perPage() === 0 ? 1 : ceil($this->total() / $this->perPage()),
            ]
        ];
    }
}
