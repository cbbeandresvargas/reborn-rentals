<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLocation extends Model
{
    use HasFactory;
    
    protected $table = 'job_locations';
    
    protected $fillable = [
        'latitude',
        'longitude',
        'date',
        'end_date',
        'time',
        'notes',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasOne(Order::class, 'job_id');
    }
}
