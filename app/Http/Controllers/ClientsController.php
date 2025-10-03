<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoryService;
use App\Http\Services\ClientService;

class ClientsController
{
    public function getClientByCategory($categoryCode)
    {
        try {
            $categoryCode = strtoupper($categoryCode);
            $isCategoryCodeValid = CategoryService::validateCategoryCode($categoryCode);

            if (!$isCategoryCodeValid) {
                abort(400, 'Invalid category code.');
            }

            $toCount = request('to_count') === 'true' ? true : false;
            $clients = ClientService::getClientByCategory($categoryCode, $toCount);

            return response()->json(['data' => $clients]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function countClientsGroupedByCategory()
    {
        try {
            $groupedClients = ClientService::countClientsGroupedByCategory();

            return response()->json(['data' => $groupedClients]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
