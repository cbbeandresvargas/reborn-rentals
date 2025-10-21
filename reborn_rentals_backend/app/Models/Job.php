<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Job extends Model
{
    use HasFactory; 
     protected $fillable = [
        'latitude',
        'longitude',
        'date',
        'time',
        'notes',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'date' => 'date',
        'status' => 'boolean',
    ];

    public function orders()
    {
         return $this->hasOne(Order::class);
    }
}