<?php

declare(strict_types = 1);

namespace App\Jobs\OrderExport;

use App\Models\Order;
use DateTime;

class OrderDto
{
    /**
     * @param array<OrderItemDto> $items
     */
    public function __construct(
        public int           $orderId,
        public string        $orderStatus,
        public int           $totalAmount,
        public null|DateTime $confirmedAt,
        public null|DateTime $shippedAt,
        public CustomerDto   $customer,
        public array         $items,
    )
    {
    }

    public static function makeFromOrder(Order $order): self
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = OrderItemDto::makeFromOrderItem($item);
        }

        return new self(
            orderId: $order->id,
            orderStatus: $order->status->value,
            totalAmount: $order->total_amount,
            confirmedAt: $order->confirmed_at,
            shippedAt: $order->shipped_at,
            customer: CustomerDto::makeFromCustomer($order->customer),
            items: $items,
        );
    }

    public function toArray(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->toArray();
        }

        return [
            'order_id' => $this->orderId,
            'order_status' => $this->orderStatus,
            'total_amount' => $this->totalAmount,
            'confirmed_at' => $this->confirmedAt->format('Y-m-d H:i:s'),
            'shipped_at' => $this->shippedAt->format('Y-m-d H:i:s'),
            'customer' => $this->customer->toArray(),
            'items' => $items,
        ];
    }
}
