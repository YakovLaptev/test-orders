<?php

declare(strict_types = 1);

namespace App\Models;

use App\Events\OrderShippedEvent;
use App\Models\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $customer_id
 * @property OrderStatusEnum $status
 * @property int $total_amount
 * @property null|DateTime $confirmed_at
 * @property null|DateTime $shipped_at
 *
 * @property Customer $customer
 * @property Collection<OrderItem> $items
 */
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasTimestamps;

    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'status',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'status' => OrderStatusEnum::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->status = OrderStatusEnum::NEW;
        });

        static::updating(function (self $order) {
            if ($order->status === OrderStatusEnum::CONFIRMED) {
                $order->confirmed_at = now();
                event(new OrderShippedEvent($order));
            } elseif ($order->status === OrderStatusEnum::SHIPPED) {
                $order->shipped_at = now();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


}
