<?php

namespace App\Helpers;

class AuthHelper
{
    public static function isAdmin($user): bool
    {
        // seguridad + compatibilidad
        if (!$user) return false;
        return (bool) data_get($user, 'is_admin', false);
    }
}