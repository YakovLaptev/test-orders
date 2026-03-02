<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderItem;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderService
{
    /**
     * @param object{
     *     status:null|OrderStatusEnum,
     *     customerId:null|int,
     *     shippedFrom:null|DateTime,
     *     shippedTo:null|DateTime
     * } $filter
     */
    public function getFilteredOrders(
        object $filter,
    ): Collection
    {
        $ordersQuery = Order::query();

        if ($filter->status) {
            $ordersQuery->where('status', '=', $filter->status);
        }

        if ($filter->customerId) {
            $ordersQuery->where('customer_id', '=', $filter->customerId);
        }

        if ($filter->shippedFrom && $filter->shippedTo) {
            $ordersQuery->whereBetween('shipped_at', [$filter->shippedFrom, $filter->shippedTo]);
        }

        return $ordersQuery->with(['customer', 'items'])->orderByDesc('id')->get();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Order
    {
        return Order::query()->with(['customer', 'items'])->findOrFail($id);
    }

    /**
     * @throws Exception
     */
    public function changeStatus(int $id, OrderStatusEnum $status): bool
    {
        $order = $this->getById($id);

        $this->validateStatusChanging($order->status, $status);

        $order->status = $status;

        return $order->save();
    }

    /**
     * @throws Exception
     */
    private function validateStatusChanging(OrderStatusEnum $oldStatus, OrderStatusEnum $newStatus): void
    {
        $statusChangeRules = [
            OrderStatusEnum::NEW->value => [
                OrderStatusEnum::CONFIRMED->value,
                OrderStatusEnum::CANCELLED->value,
            ],
            OrderStatusEnum::CONFIRMED->value => [
                OrderStatusEnum::PROCESSING->value,
                OrderStatusEnum::CANCELLED->value,
            ],
            OrderStatusEnum::PROCESSING->value => [OrderStatusEnum::SHIPPED->value],
            OrderStatusEnum::SHIPPED->value => [OrderStatusEnum::COMPLETED->value],
        ];

        if (
            isset($statusChangeRules[$oldStatus->value])
            && in_array($newStatus->value, $statusChangeRules[$oldStatus->value])
        ) {
            return;
        } else {
            throw new Exception('Заказ не может быть изменен на выбранный статус.');
        }
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function createOrder(array $orderData): bool
    {
        DB::transaction(function () use ($orderData) {
            $productsStockNeed = collect();
            foreach ($orderData['items'] as $item) {
                $productsStockNeed->put($item['product_id'], $item['quantity']);
            }
            $productsStock = ProductService::checkProductsStock($productsStockNeed, true);

            $order = new Order();
            $order->customer_id = $orderData['customer_id'];
            $order->total_amount = 0;
            $order->save();

            foreach ($orderData['items'] as $item) {
                $orderItem = new OrderItem();
                $orderItem->fill($item);
                $orderItem->order_id = $order->id;
                //TODO: цены нужно считать на бэкенде, однако в данной версии API сохраняем данные как есть
                // $orderItem->total_price = $orderItem->quantity * $orderItem->unit_price;
                $orderItem->save();

                $product = $productsStock->get($orderItem->product_id);
                $product->stock_quantity = $product->stock_quantity - $orderItem->quantity;
                $product->save();

                $order->total_amount += $orderItem->total_price;
            }

            if ($order->isDirty('total_amount')) {
                $order->save();
            }

            return true;
        });

        return false;
    }


}
