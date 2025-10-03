<?php

namespace App\Jobs;

use App\Http\Services\SyncService;
use App\Models\clients;
use App\Models\orders;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncOrdersJob implements ShouldQueue
{
    use Queueable;
    private $page;
    private $startDate;
    private $clientCode;

    /**
     * Create a new job instance.
     */
    public function __construct($clientCode, $page, $startDate)
    {
        $this->clientCode = $clientCode;
        $this->page = $page;
        $this->startDate = $startDate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = clients::where('code', $this->clientCode)->first();

        if (!$client) {
            abort(404, 'Client not found: ' . $this->clientCode);
        }

        $response = SyncService::fetchClientOrders($this->clientCode, $this->page, 100, $this->startDate);
        $lastPage = $response['totalPages'];
        $orders = $response['data'];
        $orders = array_map(fn($order) => [
            'client_id' => $client->id,
            'price' => floatval($order['value']) * 100,
            'created_at' => $order['date']
        ], $orders);

        if (count($orders) > 0) {
            orders::upsert($orders, ['client_id'], ['price', 'created_at']);
        }

        if ($this->page < $lastPage) {
            SyncOrdersJob::dispatch($this->clientCode, $this->page + 1, $this->startDate)->onQueue('orders_sync');
        }
    }
}
