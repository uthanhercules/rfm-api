<?php

namespace App\Http\Services;

use GuzzleHttp\Client as HttpClient;
use App\Models\clientOrderSummary;
use App\Http\Services\CategoryService;

class ClientService
{
    protected $http;


    public function __construct()
    {
        $this->http = new HttpClient();
    }

    static function getClientByCategory($categoryCode, $toCount = false)
    {
        list($recencyInterval, $frequencyMonetaryInterval) = self::getRecencyFrequencyMonetaryIntervals($categoryCode);

        $clientsQuery = clientOrderSummary::query()
            ->select('name', 'recency', 'frequency', 'total_price_of_orders as total_spent')
            ->whereRaw('recency > ? AND recency <= ?', $recencyInterval)
            ->whereRaw('(frequency + monetary)/2 > ? AND (frequency + monetary)/2 <= ?', $frequencyMonetaryInterval);

        if ($toCount) {
            return $clientsQuery->count();
        }

        $clientsQuery = $clientsQuery->get();
        return $clientsQuery;
    }

    static function countClientsGroupedByCategory()
    {
        $allCategories = CategoryService::listCategories();
        $toCount = true;
        $result = [];

        foreach ($allCategories as $category) {
            $clients = self::getClientByCategory($category->code, $toCount);
            $result[] = [
                'name' => $category->name,
                'code' => $category->code,
                'total' => $clients
            ];
        }

        return $result;
    }

    private static function getRecencyFrequencyMonetaryIntervals($categoryCode)
    {
        $recencyInterval = [];
        $frequencyMonetaryInterval = [];

        switch ($categoryCode) {
            case 'CHAMPIONS':
                $recencyInterval = [4, 5];
                $frequencyMonetaryInterval = [4, 5];
                break;
            case 'LOYAL':
                $recencyInterval = [2, 5];
                $frequencyMonetaryInterval = [3, 5];
                break;
            case 'POTENTIAL_LOYAL_CUSTOMERS':
                $recencyInterval = [3, 5];
                $frequencyMonetaryInterval = [1, 3];
                break;
            case 'RECENT':
                $recencyInterval = [4, 5];
                $frequencyMonetaryInterval = [0, 1];
                break;
            case 'PROMISING':
                $recencyInterval = [3, 4];
                $frequencyMonetaryInterval = [0, 1];
                break;
            case 'NEED_ATTENTION':
                $recencyInterval = [2, 3];
                $frequencyMonetaryInterval = [2, 3];
                break;
            case 'ABOUT_TO_SLEEP':
                $recencyInterval = [2, 3];
                $frequencyMonetaryInterval = [0, 2];
                break;
            case 'DO_NOT_LOSE':
                $recencyInterval = [0, 2];
                $frequencyMonetaryInterval = [4, 5];
                break;
            case 'AT_RISK':
                $recencyInterval = [0, 2];
                $frequencyMonetaryInterval = [2, 4];
                break;
            case 'HIBERNATING':
                $recencyInterval = [1, 2];
                $frequencyMonetaryInterval = [1, 2];
                break;
            case 'LOST':
                $recencyInterval = [0, 2];
                $frequencyMonetaryInterval = [0, 2];
                break;
            default:
                $recencyInterval = [0, 5];
                $frequencyMonetaryInterval = [0, 5];
                break;
        }

        return [$recencyInterval, $frequencyMonetaryInterval];
    }
}
