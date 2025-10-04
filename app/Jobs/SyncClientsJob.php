<?php

namespace App\Jobs;

use App\Http\Services\SyncService;
use App\Models\clients;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncClientsJob implements ShouldQueue
{
    use Queueable;
    private $page;
    private $startDate;

    /**
     * Create a new job instance.
     */
    public function __construct($page, $startDate)
    {
        $this->page = $page;
        $this->startDate = $startDate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = SyncService::fetchClients($this->page, 100, $this->startDate);
        $lastPage = $response['totalPages'] ?? 1;
        $clients = $response['data'];
        $clients = array_values(array_filter(
            $clients,
            fn($client) => (!empty($client['name'])) && !empty($client['date_last_order'])
        ));
        $clients = array_map(fn($client) => [
            'code' => $client['id'],
            'name' => $client['name']
        ], $clients);

        clients::upsert($clients, ['code'], ['name']);

        if ($this->page < $lastPage) {
            SyncClientsJob::dispatch($this->page + 1, $this->startDate)->onQueue('clients_sync');
        }

        if ($this->page === $lastPage) {
            echo "Sincronização de clientes concluída.\n";

            $allClients = clients::all();

            foreach ($allClients as $client) {
                SyncOrdersJob::dispatch($client->code, 1, $this->startDate)->onQueue('orders_sync');
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        echo "Job falhou: " . $exception->getMessage() . "\n";
        echo $exception->getTraceAsString() . "\n";
    }
}
