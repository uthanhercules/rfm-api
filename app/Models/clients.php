<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class clients extends Model
{
    protected $table = 'clients';
    public $timestamps = false;
    protected $fillable = ['name', 'code'];  
}
