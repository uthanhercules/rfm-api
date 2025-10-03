<?php

namespace App\Http\Services;

use GuzzleHttp\Client as HttpClient;
use App\Models\orders;
use App\Models\clients;

class OrderService
{
    protected $http;
    private $API_KEY;
    private $API_URL;
    private $NOW = '2024-01-01';

    public function __construct()
    {
        $this->http = new HttpClient();
        $this->API_URL = env('CO_API_BASE_URL');
        $this->API_KEY = env('CO_API_KEY');
    }
}
