<?php

namespace App\Http\Controllers;

use App\Http\Services\OrderSummaryService;
use App\Http\Services\SyncService;

class SyncController
{
    private $orderSummaryService;

    public function __construct(OrderSummaryService $orderSummaryService)
    {
        $this->orderSummaryService = $orderSummaryService;
    }

    public function syncClients()
    {
        try {
            $clients = SyncService::syncClients('2024-01-01');

            return response()->json(['message' => 'Clients synced successfully', 'data' => $clients]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function syncOrders($clientCode = null)
    {
        try {
            $orders = SyncService::syncOrders($clientCode, '2024-01-01');

            return response()->json(['data' => $orders]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function generateClientOrderSummary()
    {
        try {
            $this->orderSummaryService->generate();

            return response()->json(['message' => 'Client order summary generated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
