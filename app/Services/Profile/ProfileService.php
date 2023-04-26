<?php

namespace App\Services\Profile;



class ProfileService
{
    public static function log($data) {
        if (PHP_SAPI === 'cli')
            var_dump($data);
    }

    public static function walletBalance($user)
    {
        return ($user && $user->walletBalance) ? ($user->walletBalance->balance) : (null);
    }

}
