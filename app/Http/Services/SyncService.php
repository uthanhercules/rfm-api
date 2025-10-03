<?php

namespace App\Http\Services;

use App\Jobs\SyncClientsJob;
use GuzzleHttp\Client as HttpClient;

class SyncService
{
  static function fetchClients($page = 1, $perPage = 100, $startDate = '2024-01-01')
  {
    $response = (new HttpClient())->request('GET', config('services.co_api.base_url') . '/clients', [
      'headers' => [
        'Authorization' => 'Bearer ' . config('services.co_api.api_key'),
      ],
      'query' => [
        'perPage' => $perPage,
        'page' => $page,
        'filters[dateLastOrderStart]' => $startDate,
      ]
    ]);

    return json_decode($response->getBody(), true);
  }

  static function fetchClientOrders($clientCode, $page = 1, $perPage = 100, $startDate = '2024-01-01')
  {
    $response = (new HttpClient())->request('GET', config('services.co_api.base_url') . '/clients/' . $clientCode . '/orders', [
      'headers' => [
        'Authorization' => 'Bearer ' . config('services.co_api.api_key'),
      ],
      'query' => [
        'perPage' => $perPage,
        'page' => $page,
        'filters[dateLastOrderStart]' => $startDate,
      ]
    ]);

    return json_decode($response->getBody(), true);
  }

  static function syncClients($startDate = null)
  {
    if (!$startDate) {
      $startDate = '2024-01-01';
    }

    SyncClientsJob::dispatch(1, $startDate)->onQueue('clients_sync');
  }
}
