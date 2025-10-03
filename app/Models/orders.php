<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    protected $table = 'orders';
    public $timestamps = false;
    protected $fillable = ['client_id', 'created_at', 'price'];
}
