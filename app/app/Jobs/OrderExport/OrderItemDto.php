<?php

declare(strict_types = 1);

namespace App\Jobs\OrderExport;

use App\Models\OrderItem;

class OrderItemDto
{
    public function __construct(
        public int   $productId,
        public int   $quantity,
        public float $unitPrice,
        public float $totalPrice,
    )
    {
    }

    public static function makeFromOrderItem(OrderItem $item): self
    {
        return new self(
            productId: $item->product_id,
            quantity: $item->quantity,
            unitPrice: $item->unit_price,
            totalPrice: $item->total_price,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'order_status' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total_price' => $this->totalPrice,
        ];
    }
}
