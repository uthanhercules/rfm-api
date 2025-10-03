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
            $clients = SyncService::syncClients();

            return response()->json(['message' => 'Client sync initiated', 'data' => $clients]);
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
