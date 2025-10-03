<?php

namespace App\Http\Services;

use GuzzleHttp\Client as HttpClient;
use App\Models\clients;
use App\Models\orders;

class SyncService
{

  private static function fetchClients($page = 1, $perPage = 100, $startDate = '2024-01-01')
  {
    $response = (new HttpClient())->request('GET', env('CO_API_BASE_URL') . '/clients', [
      'headers' => [
        'Authorization' => 'Bearer ' . env('CO_API_KEY'),
      ],
      'query' => [
        'perPage' => $perPage,
        'page' => $page,
        'filters[dateLastOrderStart]' => $startDate,
      ]
    ]);

    return json_decode($response->getBody(), true);
  }

  private static function fetchClientOrders($clientCode, $page = 1, $perPage = 100, $startDate = '2024-01-01')
  {
    $response = (new HttpClient())->request('GET', env('CO_API_BASE_URL') . '/clients/' . $clientCode . '/orders', [
      'headers' => [
        'Authorization' => 'Bearer ' . env('CO_API_KEY'),
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
      $startDate = date('Y-m-d');
    }

    $allClients = [];
    $page = 1;
    $lastPage = null;

    do {
      $response = self::fetchClients($page, 100, $startDate);

      if ($lastPage === null) {
        // $lastPage = $response['totalPages'];
        $lastPage = 2;
      }

      $clients = $response['data'];
      $clients = array_values(array_filter(
        $clients,
        fn($client) => (!empty($client['name'])) && !empty($client['date_last_order'])
      ));
      $clients = array_map(fn($client) => [
        'code' => $client['id'],
        'name' => $client['name']
      ], $clients);
      $allClients = array_merge($allClients, $clients);

      if (count($allClients) >= 500 || $page == $lastPage) {
        clients::upsert($allClients, ['code'], ['name']);

        $allClients = [];
      }

      $page++;
    } while ($page <= $lastPage);

    return $allClients;
  }

  // default today
  public static function syncOrders($clientCode, $startDate = null)
  {
    if (!$startDate) {
      $startDate = date('Y-m-d');
    }

    $allOrders = [];
    $page = 1;
    $lastPage = null;
    $client = clients::where('code', $clientCode)->get()->first();

    if (!$client) {
      return null;
    }

    do {
      $response = self::fetchClientOrders($clientCode, $page, 100, $startDate);

      if ($lastPage === null) {
        $lastPage = $response['totalPages'];
      }

      $orders = $response['data'];
      $orders = array_map(fn($order) => [
        'client_id' => $client->id,
        'price' => floatval($order['value']) * 100,
        'created_at' => $order['date']
      ], $orders);
      $allOrders = array_merge($allOrders, $orders);

      if (count($orders) >= 500 || $page == $lastPage) {
        orders::upsert($orders, ['client_id'], ['price', 'created_at']);
      }

      $page++;
    } while ($page <= $lastPage);

    return $allOrders;
  }
}
