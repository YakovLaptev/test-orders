<?php

namespace App\Http\Requests\Orders;

use App\Models\Enums\OrderStatusEnum;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class GetListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::in(OrderStatusEnum::cases())],
            'shipped_from' => ['nullable', 'date'],
            'shipped_to' => ['nullable', 'date'],
        ];
    }

    /**
     * @return object{
     *     status:null|OrderStatusEnum,
     *     customerId:null|int,
     *     shippedFrom:null|DateTime,
     *     shippedTo:null|DateTime
     * }
     */
    public function makeFilter(): object
    {
        $filter = new class {
            public function __construct(
                public ?OrderStatusEnum $status = null,
                public ?int             $customerId = null,
                public ?DateTime        $shippedFrom = null,
                public ?DateTime        $shippedTo = null,
            )
            {
            }
        };

        $filter->status = OrderStatusEnum::tryFrom($this->query('name'));
        $filter->customerId = $this->query('customer_id');
        $filter->shippedFrom = Carbon::parse($this->query('shipped_from'));
        $filter->shippedTo = Carbon::parse($this->query('shipped_to'));

        return $filter;
    }
}
