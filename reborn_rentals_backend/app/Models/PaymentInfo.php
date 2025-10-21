<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentInfo extends Model
{
    use HasFactory;
    protected $fillable = [
            'user_id',
            'card_holder_name',
            'card_number',
            'card_expiration',
            'cvv',
        ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}