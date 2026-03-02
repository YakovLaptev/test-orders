<?php

declare(strict_types = 1);

namespace App\Http\Requests\Orders;

use App\Models\Enums\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(OrderStatusEnum::cases())],
        ];
    }

    public function getStatus(): OrderStatusEnum
    {
        return OrderStatusEnum::from($this->input('status'));
    }
}
