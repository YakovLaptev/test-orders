<?php

declare(strict_types = 1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'sku' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
        ];
    }

    /**
     * @return object{name:null|string, sku:null|string, category:null|string}
     */
    public function makeFilter(): object
    {
        $filter = new class {
            public function __construct(
                public ?string $name = null,
                public ?string $sku = null,
                public ?string $category = null
            )
            {
            }
        };

        $filter->name = $this->query('name');
        $filter->sku = $this->query('sku');
        $filter->category = $this->query('category');

        return $filter;
    }
}
