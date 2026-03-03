<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Jobs\OrderExport\OrderDto;
use App\Models\Order;
use App\Models\OrderExports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ExportOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    private Order $order;
    private int $orderExportId;

    public function __construct(Order $order, int $orderExportId)
    {
        $this->order = $order;
        $this->orderExportId = $orderExportId;
    }

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $data = Http::post(config('services.order_export.url'), OrderDto::makeFromOrder($this->order));

        if ($data->successful()) {
            OrderExports::query()->find($this->orderExportId)->update(['exported_at' => now()]);
        }
    }
}
