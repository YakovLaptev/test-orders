<?php

declare(strict_types = 1);

use App\Models\Customer;
use App\Models\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderExports;
use App\Models\Product;
use function \Pest\Laravel\assertDatabaseHas;

test('Created Order with stock checking', function () {
    $customer = Customer::factory()->create();
    $productsStockQuantity = 15;
    $orderItemQuantity = 5;
    $products = Product::factory(5)->create(['stock_quantity' => $productsStockQuantity]);
    $items = [];
    $products->each(function (Product $product) use (&$items, $orderItemQuantity) {
        $items[] = [
            'product_id' => $product->id,
            'quantity' => $orderItemQuantity,
        ];
    });

    $response = $this->post('/api/v1/orders', [
        'customer_id' => $customer->id,
        'items' => $items,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status', OrderStatusEnum::NEW)
        ->assertJsonPath('data.customer.name', $customer->name)
        ->assertJsonPath('data.order_items.0.product_id', $products[0]->id)
        ->assertJsonPath('data.order_items.1.product_id', $products[1]->id)
        ->assertJsonCount(count($items), 'data.order_items');

    foreach ($products as $product) {
        assertDatabaseHas(new Product()->getTable(), [
            'id' => $product->id,
            'stock_quantity' => $productsStockQuantity - $orderItemQuantity,
        ]);
    }
})->skip();

test('Change Order status with job checking', function () {
    $customer = Customer::factory()->create();
    $productsStockQuantity = 15;
    $orderItemQuantity = 5;
    $products = Product::factory(5)->create(['stock_quantity' => $productsStockQuantity]);
    $items = [];
    $products->each(function (Product $product) use (&$items, $orderItemQuantity) {
        $items[] = [
            'product_id' => $product->id,
            'quantity' => $orderItemQuantity,
        ];
    });

    $response = $this->post('/api/v1/orders', [
        'customer_id' => $customer->id,
        'items' => $items,
    ]);

    $response->assertStatus(200);

    //TODO: в будущем нужно будет переделать на создание тестовых данных фабриками
    $order = $response->json('data');

    Queue::fake();

    $response = $this->patch('/api/v1/orders/' . $order['id'] . '/status', [
        'status' => OrderStatusEnum::CONFIRMED->value,
    ]);

    $response->assertStatus(200);

    assertDatabaseHas(new Order()->getTable(), [
        'id' => $order['id'],
        'status' => OrderStatusEnum::CONFIRMED,
    ]);

    Queue::assertPushed(\App\Jobs\ExportOrderJob::class);

    assertDatabaseHas(new OrderExports()->getTable(), [
        'order_id' => $order['id'],
    ]);
});
