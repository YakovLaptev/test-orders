<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'confirmed_at' => $this->confirmed_at,
            'shipped_at' => $this->shipped_at,

            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'order_items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
