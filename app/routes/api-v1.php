<?php

declare(strict_types = 1);

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductsController::class, 'getList']);
Route::get('/orders', [OrderController::class, 'getList']);
Route::get('/orders/{id}', [OrderController::class, 'getById']);
Route::post('/orders', [OrderController::class, 'createOrder']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
