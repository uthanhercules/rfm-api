<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clientOrderSummary extends Model
{
    protected $table = 'client_order_summary';
    public $timestamps = false;
    protected $fillable = [
        'client_id',
        'name',
        'number_of_orders',
        'total_price_of_orders',
        'most_recent_order_date',
        'recency',
        'frequency',
        'monetary'
    ];
}
