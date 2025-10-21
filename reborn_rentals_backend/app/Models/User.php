<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'second_last_name',
        'phone_number',
        'address',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function paymentInfos()
    {
        return $this->hasMany(\App\Models\PaymentInfo::class);
    }

    /* =========================
     |  Métodos requeridos JWT
     |=========================*/

    /**
     * Identificador principal que irá dentro del token (sub).
     * Normalmente el ID del usuario.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Claims personalizados que quieras agregar al token.
     * Devuelve un array; déjalo vacío si no necesitas extras.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}