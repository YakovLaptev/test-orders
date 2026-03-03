<?php

declare(strict_types = 1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $order_id
 * @property DateTime $exported_at
 *
 * @property Order $order
 */
class OrderExports extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'order_id',
        'exported_at',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    protected function casts(): array
    {
        return [
            'exported_at' => 'datetime',
        ];
    }
}
