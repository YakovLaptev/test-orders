<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\OrderShippedEvent;
use App\Jobs\ExportOrderJob;
use App\Models\OrderExports;

class OrderShippedListener
{
    public function __construct()
    {
    }

    public function handle(OrderShippedEvent $event): void
    {
        $orderExport = new OrderExports(['order_id' => $event->order->id]);
        $orderExport->save();

        ExportOrderJob::dispatch($event->order, $orderExport->id);
    }
}
