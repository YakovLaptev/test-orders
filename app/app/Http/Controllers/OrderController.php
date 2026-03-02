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
            return response(status: 404)->json(['data' => null, 'message' => 'Заказ не найден']);
        }
    }

    public function updateStatus(int $id, ChangeStatusRequest $request): JsonResponse
    {
        try {
            $statusChange = $this->service->changeStatus($id, $request->getStatus());

            return response()->json(['data' => $statusChange]);
        } catch (\Exception $exception) {
            return response(status: 409)->json(['data' => null, 'message' => $exception->getMessage()]);
        }
    }

    public function createOrder(CreateRequest $request): JsonResponse
    {
        try {
            $statusCreate = $this->service->createOrder($request->validated());

            return response()->json(['data' => $statusCreate]);
        } catch (\Throwable $exception) {
            return response(status: $exception->getCode())->json(['message' => $exception->getMessage()]);
        }
    }
}
