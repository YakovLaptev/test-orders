<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Orders\ChangeStatusRequest;
use App\Http\Requests\Orders\CreateRequest;
use App\Http\Requests\Orders\GetListRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function getList(GetListRequest $request): JsonResponse
    {
        $orders = $this->service->getFilteredOrders($request->makeFilter());

        return response()->json(['data' => OrderResource::collection($orders)]);
    }

    public function getById(int $id): JsonResponse
    {
        try {
            return response()->json(['data' => OrderResource::make($this->service->getById($id))]);
        } catch (ModelNotFoundException) {
            return response()->json(['data' => null, 'message' => 'Заказ не найден'], 404);
        }
    }

    public function updateStatus(int $id, ChangeStatusRequest $request): JsonResponse
    {
        try {
            $statusChange = $this->service->changeStatus($id, $request->getStatus());

            return response()->json(['data' => $statusChange]);
        } catch (\Exception $exception) {
            return response()->json(['data' => null, 'message' => $exception->getMessage()], 409);
        }
    }

    public function createOrder(CreateRequest $request): JsonResponse
    {
        try {
            $order = $this->service->createOrder($request->validated());

            return response()->json(['data' => OrderResource::make($order)]);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
